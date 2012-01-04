<?php

/**
 * Recent Carthrob Entries Widget
 *
 * Display static listing of 10 most recent carthrob orders entries.
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Widgets
 * @author		Marc Miller
 * @link		http://bigoceanstudios.com
 */


class Wgt_carthrob_orders
{
	public 	$title;
	public $wclass;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->title = 'Recent Store Orders';
		$this->wclass = 'contentMenu';

		$this->EE =& get_instance();
	}

	// ----------------------------------------------------------------

	/**
	 * Index Function
	 *
	 * @return 	string
	 */
	public function index()
	{
		// get the Carthrob Orders channel from the DB
		$ct_query = $this->EE->db->select('value')
														->from('cartthrob_settings')
														->where('cartthrob_settings.key', 'orders_channel')
														->get();
		if ($ct_query->num_rows() > 0)
		{
			$channel_id = $ct_query->row('value');
		}

		// get the Carthrob Orders Total Field from the DB
		$ct_query = $this->EE->db->select('value')
														->from('cartthrob_settings')
														->where('cartthrob_settings.key', 'orders_total_field')
														->get();
		if ($ct_query->num_rows() > 0)
		{
			$total_id = $ct_query->row('value');
		}

		// get the Carthrob Customer Name Field from the DB
		$ct_query = $this->EE->db->select('value')
														->from('cartthrob_settings')
														->where('cartthrob_settings.key', 'orders_customer_name')
														->get();
		if ($ct_query->num_rows() > 0)
		{
			$name_id = $ct_query->row('value');
		}

		// get the Carthrob Customer Email Field from the DB
		$ct_query = $this->EE->db->select('value')
														->from('cartthrob_settings')
														->where('cartthrob_settings.key', 'orders_customer_email')
														->get();
		if ($ct_query->num_rows() > 0)
		{
			$email_id = $ct_query->row('value');
		}

		// get most recent 10 entries from DB
		$entries = $this->EE->db->select('t.entry_id AS entry_id, t.title AS title, t.channel_id AS channel_id, t.entry_date AS entry_date, d.field_id_'.$total_id.' AS total, d.field_id_'.$name_id.' AS name, d.field_id_'.$email_id.' AS email')
														->from('channel_titles t')
														->join('channel_data d', 't.entry_id = d.entry_id', 'inner')
														->where('t.channel_id', $channel_id)
														->order_by('t.entry_date DESC')
														->limit(10)
														->get();
		// generate table HTML
		$display = '';
		if($entries->num_rows() > 0)
		{
			foreach($entries->result() as $entry)
			{
				$display .= '
					<tr class="'.alternator('odd','even').'">
						<td><a href="'.BASE.AMP.'D=cp'.AMP.'C=content_publish'.AMP.'M=entry_form'.AMP.'channel_id='.$entry->channel_id.AMP.'entry_id='.$entry->entry_id.'">'.$entry->title.'</a></td>
						<td>'.$entry->name.'</td>
						<td>'.$entry->email.'</td>
						<td>'.$entry->total.'</td>
						<td>'.date('m/d/Y',$entry->entry_date).'</td>
					</tr>';
			}
		}
		else
		{
			$display = '<tr><td colspan="2"><center>No entries have been created.</center></td></tr>';
		}

		return '
			<table>
				<thead><tr><th>Order #</th><th>Customer</th><th>Email</th><th>Total</th><th>Order Date</th></tr></thead>
				<tbody>'.$display.'</tbody>
			</table>
		';

	}
}
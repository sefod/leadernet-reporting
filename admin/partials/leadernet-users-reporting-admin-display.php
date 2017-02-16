<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://ntmatter.com
 * @since      1.0.0
 *
 * @package    Leadernet_Users_Reporting
 * @subpackage Leadernet_Users_Reporting/admin/partials
 */

?>
<?php 

global $wpdb;
$field_orgtype = 163;
$field_country = 180;
$field_gender = 133;
$countries_qty = 25;
$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'total_members';

?> 

<div class="wrap">  
	<h2><?php _e('Members report','leadernet-ntm'); ?></h2> 
	<h2 class="nav-tab-wrapper">  
		<a href="?page=leadernet-users-reporting&tab=total_members" class="nav-tab <?php echo $active_tab == 'total_members' ? 'nav-tab-active' : ''; ?>"><?php _e('Total Members','leadernet-ntm'); ?></a>  
		<a href="?page=leadernet-users-reporting&tab=recent_members" class="nav-tab <?php echo $active_tab == 'recent_members' ? 'nav-tab-active' : ''; ?>"><?php _e('Recent Members','leadernet-ntm'); ?></a>  
	</h2> 

	<?php if( $active_tab == 'total_members' ) :

		$result = count_users();
		echo '<p>';
		echo '<strong>'.__('Total Users: ','leadernet-ntm').'</strong>'.$result['total_users'];
		echo '</p>';

		echo '<p>';
		$db_query = "SELECT * FROM wp_bp_xprofile_data WHERE field_id = $field_orgtype AND value LIKE '%MSH Employee%'";
		$match_ids = $wpdb->get_results($db_query);
		echo '<strong>'.__('Total MSH Staff: ','leadernet-ntm').'</strong>'.count($match_ids);
		echo '</p>';

		echo '<p>';
		$db_query = "SELECT COUNT(DISTINCT value) as total from wp_bp_xprofile_data WHERE field_id = $field_country";
		$countries= $wpdb->get_results($db_query);
		echo '<strong>'.__('Total Countries: ','leadernet-ntm').'</strong>'.$countries[0]->total;
		echo '</p>';

		echo '<p>';
		$db_query = "SELECT value,COUNT(*) as count FROM wp_bp_xprofile_data WHERE field_id = $field_country GROUP BY value ORDER BY count DESC LIMIT $countries_qty";
		$top25countries= $wpdb->get_results($db_query);
		echo '<strong>'.'Top '.$countries_qty.' countries: '.'</strong>';
		echo '<br>';

		echo '<ul>';
		foreach ($top25countries as $country) {
			echo '<li>- '.stripslashes($country->value).'<li>';
		};
		echo '</ul>';
		echo '</p>';

		$db_query = "SELECT * FROM wp_bp_xprofile_data WHERE field_id = $field_gender";
		$gendertotal = $wpdb->get_results($db_query);
		$gendertotal = count($gendertotal);

		$db_query = "SELECT value,COUNT(*) as count FROM wp_bp_xprofile_data WHERE field_id = $field_gender GROUP BY value ORDER BY count DESC";
		$genders = $wpdb->get_results($db_query);

		echo '<p>';
		echo '<strong>'.__('Gender Distribution: ','leadernet-ntm') .'</strong><br>';
		echo '<ul>';
		foreach ($genders as $gender) {
			echo '<li>- '.$gender->value.': '.round($gender->count/$gendertotal*100).'% ('.$gender->count.')<li>';
		};
		echo '</ul>';
		echo '<small>'.__('Gender Distribution Total Count may vary from total users count since field is optional.','leadernet-ntm').'</small>';
		echo '</p>';


		$db_query = "SELECT * FROM wp_bp_xprofile_data WHERE field_id = $field_orgtype";
		$organization_types = $wpdb->get_results($db_query);

		$total_orgs = array();
		foreach ($organization_types as $organization_type) {
			$organizations = maybe_unserialize($organization_type->value);
			foreach ($organizations as $organization) {
				$total_orgs[] = $organization;
			}
		}
		echo '<p>';
		echo '<strong>'.__('Organizational Type Distribution: ','leadernet-ntm').'</strong><br>';

		$total_orgs_count = count($organization_types);
		$total_orgs = array_count_values($total_orgs);
		arsort($total_orgs);

		echo '<ul>';
		foreach ($total_orgs as $orgtype => $typecount) {
			echo '<li>- '.$orgtype.': '.round($typecount/$total_orgs_count*100).'%</li>';
		}
		echo '</ul>';

		echo '<small>'.__('Organizational type percentages are based on the % of the total users that match that organization type, since a user can have multiple types.','leadernet-ntm').'</small>';
		echo '</p>';	

	elseif( $active_tab == 'recent_members' ) : 

		$month_ago= date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
		$start_date = (empty($_GET['dt_start'])) ? $month_ago : $_GET['dt_start'];
		$end_date = (empty($_GET['dt_end'])) ? date( "Y-m-d") : $_GET['dt_end'];
		?>
		
		<p>
		<form action="<?php echo($_SERVER["PHP_SELF"]);?>">
		  <?php _e('Period:','leadernet-ntm');?>
		  <input type="hidden" name="page" value="leadernet-users-reporting">
		  <input type="hidden" name="tab" value="recent_members">
		  <input type="date" name="dt_start" min="2000-01-01" value="<?php echo $start_date; ?>"> - 
		  <input type="date" name="dt_end" max="<?php echo date( "Y-m-d"); ?>" value="<?php echo $end_date; ?>">
		  <input type="submit" value="<?php _e('Change','leadernet-ntm');?>">
		</form>
		</p>

		<?php 

		if( empty($start_date) )
		  $date = date('Y-m-d');

		if ( empty($end_date) )
		  $end_date= $start_date;

		$start_dt = new DateTime($start_date. ' 00:00:00');
		$start_date = $start_dt->format('Y-m-d');

		$end_dt = new DateTime($end_date.' 23:59:59');
		$end_date = $end_dt->format('Y-m-d');

		$castdate = "CAST(U.user_registered AS DATE) BETWEEN '$start_date' AND '$end_date'";

		$db_query = "SELECT * FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE 1=1 AND $castdate GROUP BY U.ID";
		$match_ids = $wpdb->get_results($db_query);

		if(count($match_ids) != 0) {

			echo '<p>';
			echo '<strong>'.__('Total Users: ','leadernet-ntm').'</strong>'.count($match_ids);
			echo '</p>';

			echo '<p>';
			$db_query = "SELECT * FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE field_id = $field_orgtype AND value LIKE '%MSH Employee%' AND $castdate GROUP BY U.ID";
			$match_ids = $wpdb->get_results($db_query);
			echo '<strong>'.__('Total MSH Staff: ','leadernet-ntm').'</strong>'.count($match_ids);
			echo '</p>';

			echo '<p>';
			$db_query = "SELECT COUNT(DISTINCT value) as total FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE field_id = $field_country AND $castdate";
			$countries= $wpdb->get_results($db_query);
			echo '<strong>'.__('Total Countries: ','leadernet-ntm').'</strong>'.$countries[0]->total;
			echo '</p>';

			$db_query = "SELECT value,COUNT(*) as count FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE field_id = $field_country AND $castdate GROUP BY value ORDER BY count DESC LIMIT $countries_qty";
			$top25countries= $wpdb->get_results($db_query);

			echo '<p>';
			echo '<strong>'.__('Top countries: ','leadernet-ntm') .'</strong><br>';

			echo '<ul>';
			foreach ($top25countries as $country) {
				echo '<li>- '.stripslashes($country->value).'<li>';
			};
			echo '</ul>';
			echo '</p>';

			$db_query = "SELECT * FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE field_id = $field_gender AND $castdate";
			$gendertotal = $wpdb->get_results($db_query);
			$gendertotal = count($gendertotal);

			$db_query = "SELECT value,COUNT(*) as count FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE field_id = $field_gender AND $castdate GROUP BY value ORDER BY count DESC";
			$genders = $wpdb->get_results($db_query);

			echo '<p>';
			echo '<strong>'.__('Gender Distribution: ','leadernet-ntm') . '</strong><br>';
			echo '<ul>';
			foreach ($genders as $gender) {
				echo '<li>- '.$gender->value.': '.round($gender->count/$gendertotal*100).'% ('.$gender->count.')<li>';
			};
			echo '</ul>';
			echo '<small>'.__('Gender Distribution Total Count may vary from total users count since field is optional.','leadernet-ntm').'</small><br>';
			echo '<p>';


			$db_query = "SELECT * FROM wp_users U LEFT JOIN wp_bp_xprofile_data B ON U.ID = B.user_id WHERE field_id = $field_orgtype AND $castdate";
			//print_r($db_query);
			$organization_types = $wpdb->get_results($db_query);
			$total_orgs = array();
			foreach ($organization_types as $organization_type) {
				$organizations = maybe_unserialize($organization_type->value);
				foreach ($organizations as $organization) {
					$total_orgs[] = $organization;
				}
			}
			echo '<p>';
			echo '<strong>'.__('Organizational Type Distribution: ','leadernet-ntm') . '</strong><br>';

			$total_orgs_count = count($organization_types);
			$total_orgs = array_count_values($total_orgs);

			arsort($total_orgs);

			echo '<ul>';
			foreach ($total_orgs as $orgtype => $typecount) {
				echo '<li>- '.$orgtype.': '.round($typecount/$total_orgs_count*100).'% ('.$typecount.')</li>';
			}
			echo '</ul>';

			echo '<small>'.__('Organizational type percentages are based on the % of the total users that match that organization type, since a user can have multiple types.','leadernet-ntm').'</small>'; 
			echo '</p>';

		} else {
			echo '<p>'.__('No users information available for that period. Please extend the dates range','leadernet-ntm').'</p>'; 
		}


		

	endif; ?>  

</div>
<?php

	function get_all_app_items($app_id = 0, $offset = 0, $limit = 500) {
		if($app_id == 0)
			return false;

		return PodioItem::filter($app_id, array(
                                    'limit' => $limit,
                                    'offset' => $offset
                                    ));

	}

	function check_invoiced($fields = array()) {
		$invoiced = true;
		foreach($fields as $field) {
			if($field->external_id == 'invoice-status' && $field->field_id == '71430604') {
				if($field->values[0]['id'] != 2) {
					$invoiced = false;
				}
			}
		}
		return $invoiced;
	}

	function get_total_hours($app_id = 0) {
		if($app_id == 0)
			return 0;

		$invoiced_hours = 0;
		$not_invoiced_hours = 0;
		$item_ids = array();
		$app_item_ids = array();

		$item_collection = get_all_app_items($app_id);

		foreach ($item_collection as $item) {
			$item_ids[] = $item->item_id;
			$app_item_ids[] = $item->app_item_id;

			$fields = $item->fields;
			if($fields) {
				$invoiced = check_invoiced($fields);

				foreach($fields as $field) {
					if($field->external_id == 'time-spent' && $field->field_id == '71430602') {
						if($invoiced) {
                            $not_invoiced_hours += $field->values;
                        }
                        else {
                            $invoiced_hours += $field->values;
                        }
					}

				}

			}
		}

        $not_invoiced_hours = $not_invoiced_hours/3600;
        $invoiced_hours = $invoiced_hours/3600;

		return array(
            "not_invoiced_hours" => $not_invoiced_hours,
            "invoiced_hours" => $invoiced_hours,
            "total" => ($invoiced_hours + $not_invoiced_hours)
		);

	}
    
    function get_app_id($name = "") {
        if($name == null)
            return false;
        
        global $app_ids;
        foreach($app_ids as $app_name => $app_id) {
            if($name == $app_name) {
                return $app_id;
            }
        }
        return false;
    }

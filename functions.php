<?php

	function get_all_app_items($app_id = 0, $offset = 0, $limit = 500) {
		if($app_id == 0)
			return false;

		$item_collection = 0;
		try {
			$item_collection = PodioItem::filter($app_id, array(
                                    'limit' => $limit,
                                    'offset' => $offset
                                    ));
		}
		catch(PodioError $e) {
			global $redirect_uri;
			print "<div class='results'><p class='error'>There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b>. <a href='".$redirect_uri."'>Retry</a></p></div>";
		}

		return $item_collection;
	}

	function check_invoiced($fields = array()) {
		$invoiced = false;
		foreach($fields as $field) {
			if($field->external_id == 'invoice-status' && $field->field_id == '71430604') {
				if($field->values[0]['id'] == 2) {
					$invoiced = true;
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

		$item_collection = get_all_app_items($app_id);
		if($item_collection == null)
			return 0;

		foreach ($item_collection as $item) {
			/*$item_ids[] = $item->item_id;
			$app_item_ids[] = $item->app_item_id;*/

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
            return 0;

        global $app_ids;
        foreach($app_ids as $app_name => $app_id) {
            if($name == $app_name) {
                return $app_id;
            }
        }
        return 0;
    }

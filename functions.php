<?php

	function get_all_app_items($app_id = 0, $offset = 0, $limit = 500) {
		if($app_id == 0)
			return false;

		$item_collection = array();
		try {
			$item_collection = PodioItem::filter($app_id, array(
                                    'limit' => $limit,
                                    'offset' => $offset
                                 ));
		}
		catch(PodioError $e) {
			//global $redirect_uri;
			//print "<div class='results'><p class='error'>There was an erroreeee. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}</b> <a href='".$redirect_uri."'>Retry</a></p></div>";
		}

		return $item_collection;
	}

	function check_invoiced($fields = array()) {
		$invoiced = false;
		foreach($fields as $field) {
            if($field->external_id == 'invoice-status') {
                $pattern = "/(not)/";
//                if($field->values[0]['id'] == 2) {
                if(preg_match($pattern, strtolower($field->values[0]['text'])) == 0) {
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

            $fields = array();
            $fields[] = $item->fields["time-spent"];
            $fields[] = $item->fields["invoice-status"];

            if($fields) {
				$invoiced = check_invoiced($fields);

				foreach($fields as $field) {
                    if($field->external_id == 'time-spent') {
						if($invoiced) {
                            $invoiced_hours += $field->values;
                        }
                        else {
                            $not_invoiced_hours += $field->values;
                        }
					}
				}
			}
		}

        $invoiced_hours = $invoiced_hours/3600;
        $not_invoiced_hours = $not_invoiced_hours/3600;

		return array(
            "invoiced_hours" => round($invoiced_hours, 2),
            "not_invoiced_hours" => round($not_invoiced_hours, 2),
            "total" => round($invoiced_hours + $not_invoiced_hours, 2)
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

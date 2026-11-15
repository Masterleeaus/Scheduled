<?php
// Audit excerpt from private/cleanto/objects/class_dayweek_avail.php (unchanged original)
    public function insert_schedule_weekly()
    {
        $query = "insert into `" . $this->table_name . "` (`id`,`provider_id`,`week_id`,`weekday_id`,`day_start_time`,`day_end_time`,`off_day`,`provider_schedule_type`) values(NULL,'" . $this->staff_id . "','" . $this->week_id . "','" . $this->weekday_id . "','" . $this->day_start_time . "','" . $this->day_end_time . "','" . $this->off_days . "','" . $this->provider_schedule_type . "')";
        $result = mysqli_query($this->conn, $query);
        $value = mysqli_insert_id($this->conn);
        return $value;
    }
    /* Get day name by id */
    public function get_daynamebyid($id)
    {
        $name = "";
        switch ($id) {
            case 1:
                $name = "Monday";
                break;
            case 2:
                $name = "Tuesday";
                break;
            case 3:
                $name = "Wednesday";
                break;
            case 4:
                $name = "Thursday";
                break;
            case 5:
                $name = "Friday";
                break;
            case 6:
                $name = "Saturday";
                break;
            case 7:
                $name = "Sunday";
                break;
        }
        return $name;
    }


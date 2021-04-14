<?php

namespace App\Services;

class CalendarService {

    static function getCalendarWeeks($date) {
        /*         * ******************************************************************** */
// Tomamos el primer día de la semana del mes
        $weekday = date('N', strtotime($date)) - 1;
        switch ($weekday) {
            case 0: $firstDay = strtotime($date);
                break;
            case 6:
                $firstDay = strtotime($date . '+' . (7 - $weekday) . ' days');
                break;
            default: $firstDay = strtotime($date . '-' . $weekday . ' days');
                break;
        }
        /*         * ******************************************************************** */
// Y tomamos el ultimo día del mes
        $lastDay = strtotime(date('Y-m-t', strtotime($date)));
        $weekday = date('N', $lastDay);
        if ($weekday < 6)
            $lastDay = strtotime('next Saturday', $lastDay);
        elseif ($weekday == 7)
            $lastDay = strtotime(date('Y-m-d', $lastDay) . ' -1 days');


        /*         * ******************************************************************** */
// Crear calendario
        $cDay = $firstDay;
        $days = [[], [], [], [], [], []];
        $weekday = 1;
        $week = 0;
        $oneDay = 24 * 60 * 60;
        $daySP = [
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mié',
            4 => 'Jue',
            5 => 'Vie',
            6 => 'Sáb',
            0 => 'Dom',
        ];
        while ($cDay <= $lastDay) {
            if ($weekday < 7)
                $days[$week][] = [
                    'time' => $cDay,
                    'date' => date('d/m', $cDay),
                    'day' => $daySP[$weekday]
                ];
            $cDay += $oneDay;
            if ($weekday == 7) {
                $weekday = 0;
                $week++;
            }
            $weekday++;
        }
        
        
        return [
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'days' => $days,
        ];
    }

}

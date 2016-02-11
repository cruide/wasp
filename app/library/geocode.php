<?php namespace App\Library {

    class Geocode extends \Wasp\Library
    {
        protected $_curl;
        private static $_instance;

        const API_KEY = 'AIzaSyAlrOK47wXJWzd9pdvo7FZ25vKZPvZ0BP8';

// ---------------------------------------------------------------------------------
        /**
        * Предварительная инициализация
        * переменных при создании класса
        */
        public function _prepare()
        {
            $this->_curl   = new \Wasp\WCurl();
            $this->timeout = 3;
        }
// ---------------------------------------------------------------------------------
        /**
        * Получение адреса по GPS координатам
        *
        * @param float $latitude
        * @param float $longitude
        * @param string $lang
        */
        public function getByCoords($latitude, $longitude, $lang = 'ru')
        {
//            $url    = 'https://maps.googleapis.com/maps/api/geocode/json?key=' . self::API_KEY . '&latlng=' . $latitude . ',' . $longitude . '&sensor=false&language=' . $lang;
            $url    = "http://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&sensor=false&language={$lang}";
            $addr   = null;

            $curDate = date('Ymd');

           $_ = $this->getFromBase($latitude, $longitude, $lang);

            if( empty($_) ) {
                if( is_file(TEMP_DIR . DIR_SEP . "{$curDate}.txt") ) {
                    if( filemtime(TEMP_DIR . DIR_SEP . "{$curDate}.txt") > (time() - 10800) ) {
                        return false;
                    }

                    if( !(@unlink(TEMP_DIR . DIR_SEP . "{$curDate}.txt")) ) {
                        return false;
                    }
                }

                $_ = $this->_curl->get($url);
            } else {
                return $_;
            }

            if( !empty($_) ) {
                $result = json_decode($_, true);

                if( !empty($result['status']) && $result['status'] == 'OVER_QUERY_LIMIT' || $result['status'] == 'REQUEST_DENIED' ) {
                    file_put_contents( TEMP_DIR . DIR_SEP . "{$curDate}.txt", $result['status'] );
                }

                if( array_count($result['results']) > 0 ) {
                    foreach($result['results'] as $key=>$val) {
                        if( !empty($val['formatted_address']) && !empty($val['geometry']['location']) ) {
                            $this->setToBase($val['geometry']['location']['lat'], $val['geometry']['location']['lng'], $lang, $val['formatted_address']);
                        }
                    }
                }

                if( !empty($result['results']) ) {
                    $addr = array_get_first($result['results']);

                    if( !empty($addr['formatted_address']) ) $this->setToBase($latitude, $longitude, $lang, $addr['formatted_address']);
                }
            }

            unset($result, $_); return $addr['formatted_address'];
        }
// ---------------------------------------------------------------------------------
        /**
        * Получение адреса по GPS координатам
        * из внутренней БД
        *
        * @param mixed $latitude
        * @param mixed $longitude
        * @param mixed $lang
        * @return mixed
        */
        public function getFromBase($latitude, $longitude, $lang = 'ru')
        {
            $_ = \App\Models\Geocode::where('latitude', '=', $latitude)
                                    ->where('longitude', '=', $longitude)
                                    ->where('lng', '=', strtoupper($lang))
                                    ->first();

            if( empty($_->latitude) ) {

                $minLat = $latitude - 0.00050;
                $maxLat = $latitude + 0.00050;

                $minLng = $longitude - 0.00050;
                $maxLng = $longitude + 0.00050;

                $_ = \App\Models\Geocode::where('latitude', '>=', $minLat)
                                        ->where('latitude', '<=', $maxLat)
                                        ->where('longitude', '>=', $minLng)
                                        ->where('longitude', '<=', $maxLng)
                                        ->where('lng', '=', strtoupper($lang))
                                        ->first();
            }

            return (empty($_->address)) ? false : addslashes($_->address);
        }
// ---------------------------------------------------------------------------------
        /**
        * Сохранение новых данных в локальной БД
        *
        * @param float $latitude
        * @param dloat $longitude
        * @param string $lang
        * @param string $address
        */
        public function setToBase($latitude, $longitude, $lang = 'ru', $address)
        {
            if( empty($address) ) return false;

            $_ = new Geocode();
            $_->latitude  = $latitude;
            $_->longitude = $longitude;
            $_->lng       = strtoupper($lang);
            $_->address   = $address;
            
            return $_->save();
        }

// ---------------------------------------------------------------------------------
        public static function MySelf()
        {
            if( null === self::$_instance ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }
}


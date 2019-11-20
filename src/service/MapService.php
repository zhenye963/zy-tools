<?php
namespace zhenye963\tools;

class MapService
{

    const x_PI = 52.35987755982988;

    const PI = 3.1415926535897932384626;

    const a = 6378245.0;

    const ee = 0.00669342162296594323;

    /**
     * WGS84转GCj02(北斗转高德)
     * 
     * @param
     *            lng
     * @param
     *            lat
     *            @returns {*[]}
     */
    public static function wgs84togcj02($lng, $lat)
    {
        if (self::out_of_china($lng, $lat)) {
            return array(
                $lng,
                $lat
            );
        } else {
            $dlat = self::transformlat($lng - 105.0, $lat - 35.0);
            $dlng = self::transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * self::PI;
            $magic = sin($radlat);
            $magic = 1 - self::ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtmagic) * self::PI);
            $dlng = ($dlng * 180.0) / (self::a / $sqrtmagic * cos($radlat) * self::PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return array(
                $mglng,
                $mglat
            );
        }
    }

    /**
     * GCJ02 转换为 WGS84 (高德转北斗)
     * 
     * @param
     *            lng
     * @param
     *            lat
     * @return array(lng, lat);
     */
    public static function gcj02towgs84($lng, $lat)
    {
        if (self::out_of_china($lng, $lat)) {
            return array(
                $lng,
                $lat
            );
        } else {
            $dlat = self::transformlat($lng - 105.0, $lat - 35.0);
            $dlng = self::transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * self::PI;
            $magic = sin($radlat);
            $magic = 1 - self::ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / ((self::a * (1 - self::ee)) / ($magic * $sqrtmagic) * self::PI);
            $dlng = ($dlng * 180.0) / (self::a / $sqrtmagic * cos($radlat) * self::PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return array(
                $lng * 2 - $mglng,
                $lat * 2 - $mglat
            );
        }
    }

    /**
     * * 百度坐标系 (BD-09) 与 火星坐标系 (GCJ-02)的转换
     * * 即 百度 转 谷歌、高德
     * * @param bd_lon
     * * @param bd_lat
     * * @returns
     */
    public static function bd09togcj02($bd_lon, $bd_lat)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $bd_lon - 0.0065;
        $y = $bd_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $gg_lng = $z * cos(theta);
        $gg_lat = $z * sin(theta);
        return array(
            $gg_lng,
            $gg_lat
        );
    }

    /**
     * GCJ-02 转换为 BD-09 （火星坐标系 转百度即谷歌、高德 转 百度）
     * 
     * @param
     *            $lng
     * @param $lat @returns
     *            array(bd_lng, bd_lat)
     */
    public static function gcj02tobd09($lng, $lat)
    {
        $z = sqrt($lng * $lng + $lat * $lat) + 0.00002 * Math . sin($lat * x_PI);
        $theta = Math . atan2($lat, $lng) + 0.000003 * Math . cos($lng * x_PI);
        $bd_lng = $z * cos($theta) + 0.0065;
        $bd_lat = z * sin($theta) + 0.006;
        return array(
            $bd_lng,
            $bd_lat
        );
    }

    private static function transformlat($lng, $lat)
    {
        $ret = - 100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * self::PI) + 20.0 * sin(2.0 * $lng * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lat * self::PI) + 40.0 * sin($lat / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($lat / 12.0 * self::PI) + 320 * sin($lat * self::PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    private static function transformlng($lng, $lat)
    {
        $ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * self::PI) + 20.0 * sin(2.0 * $lng * self::PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lng * self::PI) + 40.0 * sin($lng / 3.0 * self::PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($lng / 12.0 * self::PI) + 300.0 * sin($lng / 30.0 * self::PI)) * 2.0 / 3.0;
        return $ret;
    }

    private static function rad($param)
    {
        return $param * self::PI / 180.0;
    }

    /**
     * 判断是否在国内，不在国内则不做偏移
     * 
     * @param
     *            $lng
     * @param $lat @returns
     *            {boolean}
     */
    private static function out_of_china($lng, $lat)
    {
        return ($lng < 72.004 || $lng > 137.8347) || (($lat < 0.8293 || $lat > 55.8271) || false);
    }
    
    //通过经纬度获取中心位置和缩放级别
    public static function getCenterPoint($maxJ,$minJ,$maxW,$minW){
        if($maxJ==$minJ&&$maxW==$minW)return [$maxJ,$maxW,9];
        $diff = $maxJ - $minJ;
        if($diff < ($maxW - $minW)) $diff = $maxW - $minW;
        $diff = intval(10000 * $diff)/10000;
        $centerJ = $minJ*1000000+1000000*($maxJ - $minJ)/2;
        $centerW = $minW*1000000+1000000*($maxW - $minW)/2;
        $zoom = self::getRoom($diff);
        return [floatval(sprintf("%.6f",$centerJ/1000000)),floatval(sprintf("%.6f",$centerW/1000000)),$zoom];
    }
    
    //根据经纬度的距离获取地图的缩放级
    private static function getRoom($diff){
        $room =    [0,  1,  2, 3, 4, 5, 6,7,8,  9,   10,  11,  12,  13, 14];
        $diffArr = [360,180,90,45,22,11,5,2.5,1.25,0.6,0.3,0.15,0.07,0.03,0];
        for($i = 0; $i < count($diffArr); $i++){
            if(($diff - $diffArr[$i]) >= 0){
                return $room[$i];
            }
        }
        return 14;
    }
    
}

?>
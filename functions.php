<?php
/**
 * 获取毫秒数
 * @return string
 */
function get_millisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    $ms = sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    return $ms;
}

/**
 * 获取用户真实的IP地址
 * @return mixed
 */
function get_real_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * 最简单的Ajax请求返回数据格式
 * @param string $msg 返回提示信息
 * @param int $code 返回标识符号
 * @param array $data 返回数据
 */
function ajax_return($msg = '', $code = 0, $data = [])
{
    $return['code'] = $code;
    $return['msg'] = $msg;
    $return['data'] = $data;
    exit(json_encode($return, JSON_UNESCAPED_UNICODE));
}

/**
 * CURL请求之GET方式
 * @param string $url 请求接口地址
 * @return bool|mixed
 */
function curl_get($url = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 不验证SSL证书。
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    list($content, $status) = array(curl_exec($ch), curl_getinfo($ch), curl_close($ch));
    return (intval($status["http_code"]) === 200) ? $content : false;
}

/**
 * CURL请求之POST方式
 * @param string $url 请求接口地址
 * @param array $data 请求参数
 * @param int $timeout 超时时间
 * @return mixed
 */
function curl_post($url = '', $data = [], $timeout = 30)
{
    $post_data = http_build_query($data, '', '&');
    header("Content-type:text/html;charset=utf-8");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    list($content, $status) = array(curl_exec($ch), curl_getinfo($ch), curl_close($ch));
    return (intval($status["http_code"]) === 200) ? $content : false;
}

/**
 * array_get方法使用"."号从嵌套数组中获取值
 * @param array $array
 * @param $key
 * @param $default
 * @return mixed|null
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) {
        return $array;
    }

    if (isset($array[$key])) {
        return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }

        $array = $array[$segment];
    }
    return $array;
}

/**
 * 将xml格式转换为数组
 * @param string $xml xml字符串
 * @return mixed
 */
function xml_to_array($xml = '')
{
    // 利用函数simplexml_load_string()把xml字符串载入对象中
    $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    // 编码对象后，再解码即可得到数组
    return json_decode(json_encode($obj), true);
}

/**
 * 返回格式化数字
 * @param int $number 待格式化数字
 * @param int $decimals 保留小数位数，默认2位
 * @param string $dec_point 整数和小数分隔符号
 * @param string $thousands_sep 整数部分每三位数读分隔符号
 * @return string
 */
function number_format_plus($number = 0, $decimals = 2, $dec_point = '.', $thousands_sep = ',')
{
    $format_num = '0.00';
    if (is_numeric($number)) {
        $format_num = number_format($number, $decimals, $dec_point, $thousands_sep);
    }
    return $format_num;
}


/**
 * 隐藏手机号中间四位数为****
 * @param string $mobile 正常手机号
 * @return mixed
 */
function replace_phone($mobile = '')
{
    $new_mobile = substr_replace($mobile, '****', 3, 4);
    return $new_mobile;
}

/**
 * 截取字符串，超出部分用省略符号显示
 * @param string $text 待截取字符串
 * @param int $length 截取长度，默认全部截取
 * @param string $rep 截取超出替换的字符串，默认为省略号
 * @return string
 */
function cut_string($text = '', $length = 0, $rep = '…')
{
    if (!empty($length) && mb_strlen($text, 'utf8') > $length) {
        $text = mb_substr($text, 0, $length, 'utf8') . $rep;
        //$text = mb_strimwidth($text, 0, $length, $rep);
    }
    return $text;
}

/**
 * 日期时间显示格式转换
 * @param int $time 时间戳
 * @return bool|string
 */
function transfer_show_time($time = 0)
{
    // 时间显示格式
    $day_time = date("m-d H:i", $time);
    $hour_time = date("H:i", $time);
    // 时间差
    $diff_time = time() - $time;
    $date = $day_time;
    if ($diff_time < 60) {
        $date = '刚刚';
    } else if ($diff_time < 60 * 60) {
        $min = floor($diff_time / 60);
        $date = $min . '分钟前';
    } else if ($diff_time < 60 * 60 * 24) {
        $h = floor($diff_time / (60 * 60));
        $date = $h . '小时前 ' . $hour_time;
    } else if ($diff_time < 60 * 60 * 24 * 3) {
        $day = floor($diff_time / (60 * 60 * 24));
        if ($day == 1) {
            $date = '昨天 ' . $day_time;
        } else {
            $date = '前天 ' . $day_time;
        }
    }
    return $date;
}

/**
 * 人民币数字小写转大写
 * @param string $number 人民币数值
 * @param string $int_unit 币种单位，默认"元"，有的需求可能为"圆"
 * @param bool $is_round 是否对小数进行四舍五入
 * @param bool $is_extra_zero 是否对整数部分以0结尾，小数存在的数字附加0,比如1960.30
 * @return string
 */
function rmb_format($money = 0, $int_unit = '元', $is_round = true, $is_extra_zero = false)
{
    // 非数字，原样返回
    if (!is_numeric($money)) {
        return $money;
    }
    // 将数字切分成两段
    $parts = explode('.', $money, 2);
    $int = isset($parts[0]) ? strval($parts[0]) : '0';
    $dec = isset($parts[1]) ? strval($parts[1]) : '';
    // 如果小数点后多于2位，不四舍五入就直接截，否则就处理
    $dec_len = strlen($dec);
    if (isset($parts[1]) && $dec_len > 2) {
        $dec = $is_round ? substr(strrchr(strval(round(floatval("0." . $dec), 2)), '.'), 1) : substr($parts [1], 0, 2);
    }
    // 当number为0.001时，小数点后的金额为0元
    if (empty($int) && empty($dec)) {
        return '零';
    }
    // 定义
    $chs = ['0', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
    $uni = ['', '拾', '佰', '仟'];
    $dec_uni = ['角', '分'];
    $exp = ['', '万'];
    $res = '';
    // 整数部分从右向左找
    for ($i = strlen($int) - 1, $k = 0; $i >= 0; $k++) {
        $str = '';
        // 按照中文读写习惯，每4个字为一段进行转化，i一直在减
        for ($j = 0; $j < 4 && $i >= 0; $j++, $i--) {
            // 非0的数字后面添加单位
            $u = $int{$i} > 0 ? $uni [$j] : '';
            $str = $chs [$int{$i}] . $u . $str;
        }
        // 去掉末尾的0
        $str = rtrim($str, '0');
        // 替换多个连续的0
        $str = preg_replace("/0+/", "零", $str);
        if (!isset($exp [$k])) {
            // 构建单位
            $exp [$k] = $exp [$k - 2] . '亿';
        }
        $u2 = $str != '' ? $exp [$k] : '';
        $res = $str . $u2 . $res;
    }
    // 如果小数部分处理完之后是00，需要处理下
    $dec = rtrim($dec, '0');
    // 小数部分从左向右找
    if (!empty($dec)) {
        $res .= $int_unit;
        // 是否要在整数部分以0结尾的数字后附加0，有的系统有这要求
        if ($is_extra_zero) {
            if (substr($int, -1) === '0') {
                $res .= '零';
            }
        }
        for ($i = 0, $cnt = strlen($dec); $i < $cnt; $i++) {
            // 非0的数字后面添加单位
            $u = $dec{$i} > 0 ? $dec_uni [$i] : '';
            $res .= $chs [$dec{$i}] . $u;
            if ($cnt == 1)
                $res .= '整';
        }
        // 去掉末尾的0
        $res = rtrim($res, '0');
        // 替换多个连续的0
        $res = preg_replace("/0+/", "零", $res);
    } else {
        $res .= $int_unit . '整';
    }
    return $res;
}

/**
 * 根据生日计算年龄
 * @param string $date 生日的年月日
 * @return int
 */
function get_age($date = '')
{
    $age = 0;
    $time = strtotime($date);
    // 日期非法，则不处理
    if (!$time) {
        return $age;
    }
    // 计算时间年月日差
    $date = date('Y-m-d', $time);
    list($year, $month, $day) = explode("-", $date);
    $age = date("Y", time()) - $year;
    $diff_month = date("m") - $month;
    $diff_day = date("d") - $day;
    // 不满周岁年龄减1
    if ($age < 0 || $diff_month < 0 || $diff_day < 0) {
        $age--;
    }
    return $age;
}

/**
 * 获取短网址链接
 * @param string $url 原始网址
 * @return string
 */
function get_short_url($url = '')
{
    // 直接请求第三方接口地址，获取短URL
    $api_url = 'http://tinyurl.com/api-create.php?url=';
    $short_url = file_get_contents($api_url . $url);
    return $short_url;
}

/**
 * 生成CSV文件
 * @param $data 二维数组
 * @param $filename 导出文件名，不需要带后缀
 */
function get_csv($data, $filename)
{
    header("Content-Type: application/vnd.ms-excel; charset=utf8");
    header("Content-Disposition: attachment;filename=" . $filename . ".csv");

    $str = '';
    foreach ($data as $row) {
        $str_arr = array();
        foreach ($row as $column) {
            $str_arr[] = '"' . str_replace('"', '""', $column) . '"'; //utf8转gb2312
        }
        $str .= implode(',', $str_arr) . PHP_EOL;
    }
    echo $str;
}
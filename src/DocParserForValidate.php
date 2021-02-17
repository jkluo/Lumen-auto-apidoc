<?php
namespace Jkluo\LumenApiDoc;

class DocParserForValidate
{
    private $params = array ();
    private $typeMaps = [
        'string' => '字符串',
        'int' => '整型',
        'float' => '浮点型',
        'boolean' => '布尔型',
        'date' => '日期',
        'array' => '数组',
        'fixed' => '固定值',
        'enum' => '枚举类型',
        'object' => '对象',
    ];
    /**
     * 解析注释
     * @param string $doc
     * @return array
     */
    public function parse($doc = '') {
        if ($doc == '') {
            return $this->params;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return $this->params;
        $comment = trim ( $comment [1] );
        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return $this->params;
        $this->parseLines ( $lines [1] );
        return $this->params;
    }

    private function parseLines($lines) {
        $desc = [];
        foreach ( $lines as $line ) {
            $parsedLine = $this->parseLine ( $line ); // Parse the line

            if ($parsedLine === false && ! isset ( $this->params ['description'] )) {
                if (isset ( $desc )) {
                    // Store the first line in the short description
                    $this->params ['description'] = implode ( PHP_EOL, $desc );
                }
                $desc = array ();
            } elseif ($parsedLine !== false) {
                $desc [] = $parsedLine; // Store the line in the long description
            }
        }
        $desc = implode ( ' ', $desc );
        if (! empty ( $desc ))
            $this->params ['long_description'] = $desc;
    }

    private function parseLine($line) {
        // trim the whitespace from the line
        $line = trim ( $line );
        if (empty ( $line ))
            return false; // Empty line

        if (strpos ( $line, '@' ) === 0) {
            if (strpos ( $line, ' ' ) > 0) {
                // Get the parameter name
                $param = substr ( $line, 1, strpos ( $line, ' ' ) - 1 );
                $value = trim(substr ( $line, strlen ( $param ) + 2 )); // Get the value
            } else {
                $param = substr ( $line, 1 );
                $value = '';
            }
            // Parse the line and return false if the parameter is valid
            if ($this->setParam ( $param, $value ))
                return false;
        }

        return $line;
    }

    private function setParam($param, $value) {
        if ($param == 'param' || $param == 'header'){
            $value = $this->formatParam( $value );
            if (empty($value)){
                return true;
            }
        }
        if ($param == 'class')
            list ( $param, $value ) = $this->formatClass ( $value );

        if($param == 'return'){
            $this->params [$param][] = $value;
        }elseif ($param == 'param'){
            $this->params [$param][] = $value['original'];
            list($k,$v) = $value['validate'];
            $this->params['validate'][$k] = $v;
        }elseif ($param == 'header'){
            $this->params [$param][] = $value['original'];
        }
        else if (empty ( $this->params [$param] )) {
            $this->params [$param] = $value;
        } else {
            $this->params [$param] = $this->params [$param] . $value;
        }
        return true;
    }

    private function formatClass($value) {
        $r = preg_split ( "[\(|\)]", $value );
        if (is_array ( $r )) {
            $param = $r [0];
            parse_str ( $r [1], $value );
            foreach ( $value as $key => $val ) {
                $val = explode ( ',', $val );
                if (count ( $val ) > 1)
                    $value [$key] = $val;
            }
        } else {
            $param = 'Unknown';
        }
        return array (
            $param,
            $value
        );
    }

    private function formatParam($string) {

        $param_arr = array_values(array_filter(explode(" ",$string)));
        if (count($param_arr) < 2){
            return null;
        }
        list($key,$validate) = $param_arr;
        $validate_arr = explode("|",$validate) ;
        $original=[
            'name'=>$key
        ];
        $value=[];
        foreach ($validate_arr as $k => $v){
            //过滤default和desc备注
            if (strpos($v,'default:') === false && strpos($v,'desc:') === false ){
                $value[] = $v;
            }
            $original = array_merge($original,$this->formatInputParam($v));
        }
        //validate验证数据
        $param['validate'] = [$key,implode($value,"|")] ;
        //apiDoc数据
        $param['original'] = $original ;
        return $param;
    }

    private function formatInputParam($str){
        $data = [];
        if ($str == 'required'){
            $data['require'] = 1;
        }elseif (isset($this->typeMaps[strtolower($str)])){
            $data['type'] = $str;
        }elseif (strpos($str,":")>0){
            $str_arr = explode(":",$str);
            $data[$str_arr[0]] = $str_arr[1];
        }
        return $data;
    }

    private function getParamType($type){
        return array_key_exists($type,$this->typeMaps) ? $this->typeMaps[$type] : $type;
    }
}
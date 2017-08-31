<?php
/**
 * @author: jessica.yang
 * @date: 2011-11-03
 * @filename: php.ac.app.php
 * @description: AC��ģʽƥ���㷨Ӧ����
 */
// �����ļ�
include("ACSearch.php");

class ACAppClass{
    private $showtimeFlag;// �Ƿ���ʾ����ʱ�䣬false������ʾ��true����ʾ��Ĭ��Ϊfalse
    /**?
     * @function ���캯��
     * @param
     * @return
     */
    public function ACAppClass(){
        $this->showtimeFlag = true;
    }
    /**
     * @function ���ַ����в��ҵ����ؼ���
     * @param string word �ؼ���
     * @param string text �����ҵ��ַ���
     * @return Array
     */
    public function findSingleWord($word, $text){
        try{
            if(strlen(trim($word))==0){
                throw new Exception("Key word's content is empty.");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }

        $arr = array(trim($word));
        return $this->findWordsInArray($arr, $text);

    }
    /**
     * @function ���ַ����в��Ҷ���ؼ���
     * @param Array words �ؼ�������
     * @param string text �����ҵ��ַ���
     * @return Array
     */
    public function findWordsInArray($words, $text){
        $len = count($words);
        try{
            if($len==0){
                throw new Exception("Array of keywords is empty.");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }
        if($this->showtimeFlag){
            $starttime = $this->getmicrotime();
        }
        echo "��ʼ����...";
        $tree = new AhoCorasick();
        try{
            for ($i=0; $i<$len; $i++) {
                if(trim($words[$i])==""){
                    throw new Exception("Key word's content is empty.");
                }
                echo "��ţ�" .($i+1)." ".$words[$i]."\n";
                $tree->add(trim($words[$i]));

            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }

        $tree->prepare();
        echo "�������....";
        if($this->showtimeFlag){
            $endtime1 = $this->getmicrotime();
            echo "<br>add run time is: ".($endtime1-$starttime)."ms<br>";
        }
        $res = array();
        $obj = $tree->search($text);
        while($obj->hasNext()){
            $result = $obj->next();
            $res = array_unique(array_merge($res, $result->getOutputs()));
        }
        if($this->showtimeFlag){
            $endtime2 = $this->getmicrotime();
            echo "<br>search run time is: ".($endtime2-$endtime1)."ms<br>";
        }
        return $res;
    }

    /**
     * @function ���ļ��в��ҹؼ���
     * @param string $keyfile �ؼ������ڵ��ļ����Ƽ�·��
     * @param string $textfile �����ҵ��������ڵ��ļ����Ƽ�·��
     * @return Array
     */
    public function findWordsInFile($keyfile, $textfile){
        /**************��ʱȥ��***********��
        /*try{
            if(!is_file($keyfile) || !is_file($textfile)){
                throw new Exception("Can not find the file.");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }
        // ���������ڵ��ļ�����Ϊ��ʱ���׳��쳣
        try{
            if(strlen(trim(file_get_contents($keyfile)))==0){
                throw new Exception("File's content is empty.");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }*/
        /*************��ʱȥ��**************/
        // ���ļ�
        $handle1 = fopen($keyfile, "r");
        $handle2 = fopen($textfile, "r");
        $arr = array();
        $contents = "";
        try{
            while (!feof($handle1)) {
                $line = trim(fgets($handle1));
                if(strlen($line)!=0){
                    $arr[] = $line;
                }
            }
            while (!feof($handle2)) {
                $line = trim(fgets($handle2));
                if(strlen($line)!=0){
                    $contents .= $line;
                }
            }
        }catch(Excption $e){
            echo $e->getMessage();
            return;
        }
        // �ر��ļ�
        fclose($handle1);
        fclose($handle2);
        return $this->findWordsInArray($arr, $contents);
    }
    /**
     * @function ��ȡʱ�������λΪ����
     * @param
     * @return float
     */
    function getmicrotime(){
        list($usec, $sec) = explode(" ",microtime());
        $value = (float)$usec+(float)$sec;
        return round($value*1000, 3);
    }
}
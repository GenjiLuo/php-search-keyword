<?php
/**
 * ����
 */
//require("./lib/AC.php");
//$obj = new ACAppClass();
//$res1 = $obj->findWordsInFile("./keyword.txt", "./content.txt");
//print_r($res1);
ini_set("display_errors", 1);
require("./lib/KeywordDict.php");
require("./lib/KeywordManager.php");
$keywordArr = readFileContent("./keyword.txt");
$keywordObj = new KeywordDict();
$startTime = microtime(true);
foreach ($keywordArr as  $keyword){
    $keywordObj->addWord($keyword);
}
$cacheArr = $keywordObj->getDictCache();

//�����ֵ仺��
$fileName = "./cache.php";
//$conentStr = "<?php\r\nreturn ".var_export($cacheArr, 1).";";
//$fp = fopen($fileName, "w+");
//fputs($fp, $conentStr);
//fclose($fp);
$dictCache = include($fileName);
$endTime = microtime(true);
$keywordObj->setDictCache($dictCache);
echo "�����ֵ��ܹ���ʱ:".($endTime - $startTime)."\n";
echo "--------------------------------------------\n";

$kManagerObj = new KeywordManager($keywordObj);
$textStr = file_get_contents("./content.txt");
$startTime1 = microtime(true);
$keywordArr = $kManagerObj->fetchAllKeyword($textStr);
print_r($keywordArr);
$endTime1 = microtime(true);
echo "�����ܹ���ʱ:".(($endTime1 - $startTime1)*1000)."ms\n";

/***********************test function ******************/
function readFileContent($fileName){
    // ���ļ�
    $handle1 = fopen($fileName, "r");
    $arr = array();
    try{
        while (!feof($handle1)) {
            $line = trim(fgets($handle1));
            if(strlen($line)!=0){
                $arr[] = $line;
            }
        }
    }catch(Excption $e){
        echo $e->getMessage();
        return;
    }
    // �ر��ļ�
    fclose($handle1);
    return $arr;
}
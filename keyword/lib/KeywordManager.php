<?php

/**
 * �ؼ��ʹ�����
 *
 * �����ַ���1�����ڵģ�2-3ǧ�������
 */
Class KeywordManager
{
    //���ֱ���
    const CHAR_ENCODINNG = "GBK";

    //�ؼ��������ֵ���
    private $keywordDict;

    //�ؼ��ʺʹ���
    private $keywordResult;

    //�Ƿ���ڷ��������ؼ���
    private $isExistKeyword = false;

    public function KeywordManager($keywordDict)
    {
        $this->keywordDict = $keywordDict;
    }

    /**
     * �����ı����ݲ��ҹؼ���
     * <pre>
     * @param string $text �ı�����
     * @param int $sType Ĭ��Ϊ1(�������еĹؼ���)��2(ֻ��֤�Ƿ��з��ϵĹؼ���)
     * @return  array ����ؼ���
     * </pre>
     */
    private function searchWord($text, $sType = 1)
    {
        $index = 0;
        $textLength = mb_strlen($text, self::CHAR_ENCODINNG);
        $currentChar = "";
        $maxWordLength = $this->keywordDict->getMaxWordLength();
        $minWordLength = $this->keywordDict->getMinWordLength();
        $fastCheck = $this->keywordDict->getFastCheck();
        $words = $this->keywordDict->getWords();
        $fastLength = $this->keywordDict->getFastLength();
        $isExists = false;

        //�Դ�Сд����
        $text = mb_convert_case($text, MB_CASE_LOWER, self::CHAR_ENCODINNG);

        while ($index < $textLength) {
            $currentChar = mb_substr($text, $index, 1, self::CHAR_ENCODINNG);

            //�ҵ���ĳ���ַ���ͷ�Ĺؼ���
            if (($fastCheck[$currentChar] & 1) == 0) {
                do {
                    $index++;
                    $currentChar = mb_substr($text, $index, 1, self::CHAR_ENCODINNG);
                } while (($index < $textLength) && (($fastCheck[$currentChar] & 1) == 0));
                if ($index >= $textLength) break;
            }

            //��ʱ�Ѿ��ж�����ǰ������ַ������ڹؼ��ʵĵ�һλ�ϣ����д���
            $jump = 1;
            for ($j = 0; $j <= min($maxWordLength, $textLength - $index - 1); $j++) {
                $current = mb_substr($text, $j + $index, 1, self::CHAR_ENCODINNG);

                //�жϵ�ǰ�ַ��Ƿ��ڶ�Ӧ��λ����, ʵ�ֿ��ٵ��ж�
                if (($fastCheck[$current] & (1 << min($j, $maxWordLength))) == 0) {
                    break;
                }

                //���жϷ��������ĳ��ȴ�����ߵ�����С����ʱ����ǰ�Ľ�ȡ�ַ����п��ܻ��ǹؼ��֣�Ҫ����ϸ���ж�
                if ($j + 1 >= $minWordLength) {
                    if (($fastLength[$currentChar] & (1 << min($j, $maxWordLength))) > 0) {
                        $sub = mb_substr($text, $index, $j + 1, self::CHAR_ENCODINNG);

                        //���ֵ��������жϣ��ó�����
                        if ((sizeof($words[$currentChar]) > 1) && ($words[$currentChar][$sub] == $sub)) {

                            if (2 == $sType) {
                                $this->isExistKeyword = true;
                                return '';
                            } else {
                                $this->keywordResult[$sub] += 1;
                            }
                        }
                    }
                }
            }

            $index += $jump;
        }
    }

    /**
     * �ı����ݳ��ֵ����йؼ���
     * <pre>
     * @param $text �ı�����
     * @return array ���غ�Ĺؼ���
     * </pre>
     */
    public function fetchAllKeyword($text)
    {
        $this->searchWord($text);
        if ($this->keywordResult) return array_keys($this->keywordResult);
        return "";
    }

    /**
     * �ı����ݳ��ֵ����йؼ��ʴ���
     * <pre>
     * @param $text �ı�����
     * @return array ���غ�Ĺؼ��ʴ���
     * </pre>
     */
    public function fetchAllKeywordTimes($text)
    {
        $this->searchWord($text);
        return $this->keywordResult;
    }

    /**
     * �ı����ݳ��ֹؼ���
     * <pre>
     * @param $text �ı�����
     * @return boolean true|false
     * </pre>
     */
    public function isExistsKeyword($text){
        $this->searchWord($text, 2);
        return $this->isExistKeyword;
    }
}
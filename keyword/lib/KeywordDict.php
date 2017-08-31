<?php

/**
 * �ؼ����ֵ�������
 */
Class  KeywordDict
{
    //��Źؼ�����󳤶�
    const MAX_WORD_LENGTH = 15;

    //���ֱ���
    const CHAR_ENCODINNG = "GBK";

    //������йؼ���
    private $words = array();

    private $maxStoreWordLength = 0;
    private $minStoreWordLength = 999999;

    //���ÿ���ַ��ڶ�Ӧλ�����Ƿ�������дʣ��Լ��ļ�λ�����д�
    private $fastPositionCheck = array();
    private $fastLengthCheck = array();

    //�ֵ仺��
    private $dictCache = array();

    /**
     * �ؼ���
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * ��󳤶�
     */
    public function getMaxWordLength()
    {
        return $this->maxStoreWordLength;
    }

    /**
     * ��С����
     */
    public function getMinWordLength()
    {
        return $this->minStoreWordLength;
    }

    /**
     * λ�ô洢
     */
    public function getFastCheck()
    {
        return $this->fastPositionCheck;
    }

    /**
     * ���ȴ洢
     */
    public function getFastLength()
    {
        return $this->fastLengthCheck;
    }

    /**
     * ��ӹؼ������ɲ����ֵ�
     */
    public function addWord($keyword)
    {
        $keyword = mb_convert_case($keyword, MB_CASE_LOWER, self::CHAR_ENCODINNG);
        $keywordLength = mb_strlen($keyword, self::CHAR_ENCODINNG);
        $this->maxStoreWordLength = max($this->maxStoreWordLength, $keywordLength);
        $this->minStoreWordLength = min($this->minStoreWordLength, $keywordLength);
        $firstChar = mb_substr($keyword, 0, 1, self::CHAR_ENCODINNG);

        //��¼ÿ���ʵ�λ��, ͨ��&�ķ�ʽ������֤
        for ($i = 0; $i < $keywordLength; $i++) {
            $currentChar = mb_substr($keyword, $i, 1, self::CHAR_ENCODINNG);
//            $strNum = $this->getIntegerFromStringGBK($currentChar);
            $this->fastPositionCheck[$currentChar] |= (1 << $i);
        }

        //��¼��ĳ���ӿ�ͷ�Ĺؼ��ֵĳ�����Ϣ������λ������Ϊ���ַ������ȼ�һ
        $this->fastLengthCheck[$firstChar] |= (1 << ($keywordLength - 1));

        //��ӹؼ���
        $this->words[$firstChar][$keyword] = $keyword;
    }

    /**
     * �����ַ�ת��Ϊ����
     * @param $singleChar  �����ַ�
     * @return  int ����
     */
    public function getIntegerFromStringGBK($singleChar)
    {
        $arr = str_split($singleChar);
        $length = sizeof($arr);
        $binStr = "";
        if ($length == 1) {
            return ord($singleChar);
        } else if ($length > 1) {
            foreach ($arr as $val) {
                $binStr .= decbin(ord($val));
            }
            return bindec($binStr);
        } else {
            return 0;
        }
    }

    /**
     * ��ȡ�ֵ�Ҫ��������
     */
    public function getDictCache(){
        $this->dictCache['words'] = $this->words;
        $this->dictCache['maxWordsLength'] = $this->maxStoreWordLength;
        $this->dictCache['minWordsLength'] = $this->minStoreWordLength;
        $this->dictCache['fastCheck'] = $this->fastPositionCheck;
        $this->dictCache['fastLength'] = $this->fastLengthCheck;

        return $this->dictCache;
    }

    /***
     * ������ɵ��ֵ����ݻ���
     */
    public function setDictCache($dictCache){
        if(is_array($dictCache) && sizeof($this->dictCache) > 1) {
            $this->dictCache = $dictCache;
        }
        $this->words = $this->dictCache['words'];
        $this->maxStoreWordLength = $this->dictCache['maxWordsLength'];
        $this->minStoreWordLength = $this->dictCache['minWordsLength'];
        $this->fastPositionCheck = $this->dictCache['fastCheck'];
        $this->fastLengthCheck = $this->dictCache['fastLength'];
    }
}
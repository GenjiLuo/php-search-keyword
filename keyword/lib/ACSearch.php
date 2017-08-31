<?php
/**
 * @author: jessica.yang
 * @date: 2011-10-24
 * @filename: php.ac.search.php
 * @description: Aho Corasick��ģʽƥ���㷨�����AC�㷨�����������׶Σ���һ����Ԥ����׶Σ����ֵ��������ɣ��ڶ������������ҽ׶Σ����ļ���ɵڶ��׶ε��������ҹ���
 */
// �����ļ�
include("ACState.php");
/**
 * @classname: AhoCorasick
 * @description: ����ʵ��AC��ģʽƥ������������㷨
 */
class AhoCorasick {
    private $root;// State���󣬱�ʾ���ڵ�
    private $prepared;// boolean���ͣ���ʾ�������Ƿ�װ����ɡ����Ϊtrue�����ʾ������ɣ����Ҳ����ټ���������
    private $arr_keys;// Array���󣬴�ŵ�һ����������
    /**
     * @function ���캯��
     * @param
     * @return
     */
    public function AhoCorasick() {
        $this->root = new State(0);
        $this->root->setFail($this->root);// ���ø��ڵ��ʧЧֵ
        $this->prepared = false;
        $this->arr_keys = array();
    }
    /**
     * @function ��ȡ���ڵ����
     * @param
     * @return State
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     *@function ���������
     *@param string $keywords Ҫ���ҵ�������
     *@return
     **/
    public function add($keywords=""){
        // ���װ�ر�־Ϊtrue�����ֹ�ټ���������
        try{
            if ($this->prepared){
                throw new Exception("can't add keywords after prepare() is called.");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }

        // ��������ʲ����ַ������ͣ���������Ϊ�գ��򷵻�
        try{
            if(!is_string($keywords) || strlen(trim($keywords))==0){
                throw new Exception("Added keywords is not string type, or content is empty.");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }
//        $keywords = trim($keywords);
        // �������ʰ��ַ�Ϊ��λת�����ַ�����
//        $words = $this->str_split_utf8($keywords);
        // ���õ�һ�㼶�������ַ�
//        $this->arr_keys = array_unique(array_merge($this->arr_keys, $words));
        // ��ȡ�����������֮������һ��Stateֵ
//        $lastState = $this->root->extendAll($words);
        // �����һ��Stateֵ������������
//        $lastState->addOutput($keywords);
    }
    /**
     *@function ����������add()���֮�����
     *@param
     *@return
     **/
    public function prepare() {
        $this->prepareFailTransitions();
        $this->prepared = true;
    }
    /**
     *@function �����ֵ�����ÿ��State�ڵ��ʧЧֵ
     *@param
     *@return
     **/
    private function prepareFailTransitions() {
        $q = array();// ��ŵ�һ�㼶������������
        foreach($this->arr_keys as $value){
            if(is_null($this->root->get($value))){
                // ��������ʲ������ڵ�һ�㼶������ӣ���������ʧЧֵΪ���ڵ�State����
                $this->root->put($value, $this->root);
            }else{
                // ���õ�һ�㼶��ʧЧֵΪ���ڵ�State���󣬲��Ұ������ʶ�Ӧ��Stateֵ��ӵ�$q������
                $this->root->get($value)->setFail($this->root);
                array_push($q, $this->root->get($value));
            }
        }
        // ��������State�ڵ��ʧЧֵ
        while(!is_null($q)) {
            // ������$q��һ��Stateֵ�Ƴ������飬�������Ƴ���Stateֵ
            $state = array_shift($q);
            // ���ȡ����$state����Ϊ�գ������ѭ��
            if(is_null($state)){
                break;
            }
            // ��ȡ$stateֵ��Ӧ����һ������������
            $keys = $state->keys();
            $cnt_keys = count($keys);
            for($i=0; $i<$cnt_keys; $i++) {
                $r = $state;
                $a = $keys[$i];
                $s = $r->get($a);
                array_push($q, $s);
                $r = $r->getFail();
                // �ݹ����ʧЧֵ��ֱ�����ڵ�Ϊֹ
                while(is_null($r->get($a))){
                    $r = $r->getFail();
                }

                $s->setFail($r->get($a));
                $s->setOutputs(array_unique(array_merge($s->getOutputs(), $r->get($a)->getOutputs())));
            }
        }
    }
    /**
     *@function ���Һ���
     *@param string words �����ҵ��ַ���
     *@return Searcher
     **/
    public function search($words){
        return new Searcher($this, $this->startSearch($words));
    }
    /**
     *@function ���Һ���
     *@param string words �����ҵ��ַ���
     *@return SearchResult
     **/
    public function startSearch($words) {
        // ����δ���ʱ�������������������
        try{
            if (!$this->prepared){
                throw new Exception("Can't start search until prepare().");
            }
        }catch(Exception $e){
            echo $e->getMessage();
            return;
        }
        // ת�������ҵ��ַ���Ϊ�ַ�����
        $arr_words = $this->str_split_utf8($words);
        // �������Һ�����
        $res = $this->continueSearch(new SearchResult($this->root, $arr_words, 0));
        return $res;
    }
    /**
     *@function �����Ĳ��Һ���
     *@param SearchResult lastResult SearchResult����
     *@return SearchResult or NULL
     **/
    public function continueSearch($lastResult) {
        // ���lastResult�����������Ϊnull���򷵻�
        if(is_null($lastResult)){
            return NULL;
        }

        $words = $lastResult->words;// �����ҵ��ַ�����
        $state = $lastResult->lastMatchedState;// ��ʼ���ҵ�Stateֵ
        $start = $lastResult->lastIndex;// ��ʼ���ҵ�λ��
        $len = count($words);
        for($i=$start; $i<$len; $i++) {
            $word = $words[$i];	// ��ȡ�����ַ�
            // �����ȡ�������ʲ����ڣ���ݹ�ת��ʧЧֵ����������ֱ�����ڵ�Ϊֹ
            while (is_null($state->get($word))){
                $state = $state->getFail();
                if($state===$this->root){
                    break;
                }
            }

            if(!is_null($state->get($word))){
                // ��ȡ�����ʶ�Ӧ��Stateֵ�������������ݣ������
                $state = $state->get($word);
                if (count($state->getOutputs())>0){
                    return new SearchResult($state, $words, $i+1);
                }
            }
        }
        return NULL;
    }

    /**
     * �ַ���ת�����ַ����飬 ��λ���ַ�
     * @param $str ת�����ַ�������
     *
     * @return array ����
     */
    function str_split_gbk($str){
        $split=1;
        $array = array();
        for($i=0; $i<strlen($str); ){
            $value = ord($str[$i]);
            if($value > 127){
                $split = 2;
            }else{
                $split=1;
            }

            $key = NULL;
            for($j = 0; $j<$split; $j++, $i++ ) {
                $key .= $str[$i];
            }
            array_push( $array, $key );
        }
        return $array;
    }

    /**
     *@function �ַ���ת�����ַ����飬��λ���ַ�
     *@param string str ת�����ַ�������
     *@return Array
     **/
    function str_split_utf8($str){
        $split=1;
        $array = array();
        for($i=0; $i<strlen($str); ){
            $value = ord($str[$i]);
            if($value > 127){
                /*if($value >= 192 && $value <= 223)
                    $split=2;
                else if($value >= 224 && $value <= 239)
                    $split=3;
                else if($value >= 240 && $value <= 247)
                    $split=4;*/
                $split = 2;
            }else{
                $split=1;
            }

            $key = NULL;
            for($j = 0; $j<$split; $j++, $i++ ) {
                $key .= $str[$i];
            }
            array_push( $array, $key );
        }
        return $array;
    }
}

///////////////////////////////////////
/**
 * @classname: SearchResult
 * @description: ��������࣬���ڴ洢�������Һ�Ľ����
 */
class SearchResult {
    var $lastMatchedState;// State�������ƥ���Stateֵ
    var $words;// Array���󣬱�����������
    var $lastIndex;// int���ͣ������ֵ�λ��
    /**
     * @function ���캯��
     * @param State state State����
     * @param Array words �����ҵ��ַ���
     * @param int index ����λ��
     * @return
     */
    public function SearchResult($state, $words=array(), $index=0) {
        $this->lastMatchedState = $state;
        $this->words = $words;
        $this->lastIndex = $index;
    }
    /**
     * @function ��ȡ���������
     * @param
     * @return Array
     */
    public function getOutputs() {
        return $this->lastMatchedState->getOutputs();
    }
    /**
     * @function ��ȡ���ҵ�λ��
     * @param
     * @return int
     */
    public function getLastIndex() {
        return $this->lastIndex;
    }
}

////////////////////////////
/**
 * @classname: Searcher
 * @description: ������
 */
class Searcher{
    private $tree;// AhoCorasick����
    private $currentResult;// SearchResult����
    /**
     * @function ���캯��
     * @param AhoCorasick tree AhoCorasick����
     * @param SearchResult result SearchResult����
     */
    public function Searcher($tree, $result) {
        $this->tree = $tree;
        $this->currentResult = $result;
    }
    /**
     * @function hasNext �����ж��Ƿ���ֵ����
     * @param
     * @param boolean true��ʾ��ֵ  false��ʾ��ֵ
     */
    public function hasNext() {
        return !is_null($this->currentResult);
    }
    /**
     * @function next ��ȡ��һ��ֵ
     * @param
     * @param �����ֵ�򷵻�SearchResult���󣬷��򷵻�NULL
     */
    public function next() {
        if (!$this->hasNext()){
            return NULL;
        }
        $result = $this->currentResult;
        $this->currentResult = $this->tree->continueSearch($this->currentResult);
        return $result;
    }
}
<?php
/**
 * @author: jessica.yang
 * @date: 2011-10-24
 * @filename: php.ac.pretreatment.php
 * @description: Aho Corasick��ģʽƥ���㷨�����AC�㷨�����������׶Σ���һ����Ԥ����׶Σ����ֵ��������ɣ��ڶ������������ҽ׶Σ����ļ���ɵ�һ�׶ε�Ԥ������
 */

/**
 * @classname: State
 * @description: ״̬�࣬���ڱ�ʾ�ֵ����е�ÿһ��״̬�ڵ�
 */
class State {
    private $depth;// int���ͣ���ʾÿһ��״̬�������ȣ���0��ʼ��ʾ
    private $edgeList;// �������б����ڰ�����״̬������������һ������State����
    private $fail;// State���󣬱�ʾ״̬����ʧЧ֮��Ҫ��ת�ĵط�
    private $outputs;// array���󣬴��ĳһ״̬�¿������������
    /**
     * @function State ���캯��
     * @param int depth ״̬���������
     * @return
     */
    public function State($depth) {
        $this->depth = $depth;
        //$this->edgeList = new SparseEdgeList();
        $this->edgeList = new DenseEdgeList();
        $this->fail = NULL;
        $this->outputs = array();
    }

    /**
     *@function extend ��ӵ���������
     *@param char character ���������ʣ�����һ����ĸ�����֡�����һ�����ֵ�
     *@return State
     **/
    public function extend($character) {
        if (!is_null($this->edgeList->get($character))){
            return $this->edgeList->get($character);
        }

        $nextState = new State($this->depth+1);
        $this->edgeList->put($character, $nextState);
        return $nextState;
    }
    /**
     *@function extendAll ���������
     *@param array contents ����������
     *@return State
     **/
    public function extendAll($contents) {
        $state = $this;
        $cnt = count($contents);
        for($i=0; $i<$cnt; $i++) {
            // ��������Ĺؼ��ʴ��ڣ���ֱ�ӷ��ظ� �ؼ���������State���󣬷�����Ӹùؼ���
            if(!is_null($state->edgeList->get($contents[$i]))){
                $state = $state->edgeList->get($contents[$i]);
            }else{
                $state = $state->extend($contents[$i]);
            }
        }
        return $state;
    }
    /**
     * @function ���������ʵ��ܳ���
     * @param
     * @return int
     */
    public function size() {
        $keys = $this->edgeList->keys();
        $result = 1;
        $length = count($keys);
        for ($i=0; $i<$length; $i++){
            $result += $this->edgeList->get($keys[$i])->size();
        }
        return $result;
    }
    /**
     * @function ��ȡ�����ؼ���������State����
     * @param char character
     * @return State
     */
    public function get($character) {
        $res = $this->edgeList->get($character);
        return $res;
    }
    /**
     * @function ��State�����������һ���������ʼ���Ӧ��Stateֵ
     * @param char character
     * @param State state
     * @return
     */
    public function put($character, $state) {
        $this->edgeList->put($character, $state);
    }
    /**
     * @function ��ȡState������һ�������йؼ���
     * @param
     * @return Array
     */
    public function keys() {
        return $this->edgeList->keys();
    }
    /**
     * @function ��ȡState����ʧЧʱ��Ӧ��ʧЧֵ
     * @param
     * @return State
     */
    public function getFail() {
        return $this->fail;
    }
    /**
     * @function ����State����ʧЧʱ��Ӧ��ʧЧֵ
     * @param
     * @return
     */
    public function setFail($state) {
        $this->fail = $state;
    }
    /**
     * @function ��State�����outputs������������
     * @param
     * @return
     */
    public function addOutput($str) {
        array_push($this->outputs, $str);
    }
    /**
     * @function ��ȡState������������
     * @param
     * @return Array
     */
    public function getOutputs() {
        return $this->outputs;
    }
    /**
     * @function ����State������������
     * @param
     * @return
     */
    public function setOutputs($arr=array()){
        $this->outputs = $arr;
    }
}

////////////////////////////////////////////////////////
/**
 * @classname: DenseEdgeList
 * @description: �洢State������һ����Ӧ������State���ݣ���������ʽ�洢
 */
class DenseEdgeList{
    private $array;// State���󣬰�����Ӧ�������ʼ�Stateֵ
    /**
     * ���캯��
     */
    public function DenseEdgeList() {
        $this->array = array();
    }
    /**
     * @function ������洢��ʽ������תΪ����洢��ʽ������
     * @param SparseEdgeList list
     * @return DenseEdgeList
     */
    public function fromSparse($list) {
        $keys = $list->keys();
        $newInstance = new DenseEdgeList();
        for($i=0; $i<count($keys); $i++) {
            $newInstance->put($keys[$i], $list->get($keys[$i]));
        }
        return $newInstance;
    }
    /**
     * @function ��ȡ�����ʶ�Ӧ��Stateֵ
     * @param char word
     * @return ��������򷵻ض�Ӧ��State���󣬷��򷵻�NULL
     */
    public function get($word) {
        if(array_key_exists($word, $this->array)){
            return $this->array["$word"];
        }else{
            return NULL;
        }
    }
    /**
     * @function ��������ʼ���Ӧ��Stateֵ��������
     * @param char word ����������
     * @param State state �����ʶ�Ӧ��State����
     * @return
     */
    public function put($word, $state) {
        $this->array["$word"] = $state;
    }
    /**
     * @function ��ȡ���е�������
     * @param
     * @return Array
     */
    public function keys() {
        return array_keys($this->array);
    }
}

///////////////////////////////////////
/**
 * @classname: SparseEdgeList
 * @description: �洢State������һ����Ӧ������State���ݣ���������ʽ�洢
 */
class SparseEdgeList{
    private $head;// Cons����
    /**
     * ���캯��
     */
    public function SparseEdgeList() {
        $this->head = NULL;
    }
    /**
     * @function ��ȡ�����ʶ�Ӧ��Stateֵ
     * @param char word
     * @return ��������򷵻ض�Ӧ��State���󣬷��򷵻�NULL
     */
    public function get($word) {
        $cons = $this->head;
        while(!is_null($cons)){
            if ($cons->word === $word){
                return $cons->state;
            }
            $cons = $cons->next;
        }
        return NULL;
    }
    /**
     * @function ��������ʼ���Ӧ��Stateֵ��������
     * @param char word ����������
     * @param State state �����ʶ�Ӧ��State����
     * @return
     */
    public function put($word, $state){
        $this->head = new Cons($word, $state, $this->head);
    }
    /**
     * @function ��ȡ���е�������
     * @param
     * @return Array
     */
    public function keys() {
        $result = array();
        $c = $this->head;
        while(!is_null($c)){
            array_push($result, $c->word);
            $c = $c->next;
        }
        return $result;
    }

}
/**
 * @classname: Cons
 * @description: ����SparseEdgeList��������ʱ��ʾ�Ľڵ����
 */
class Cons {
    var $word;// ����������
    var $state;// State����
    var $next;// Cons����
    /**
     * ���캯��
     */
    public function Cons($word, $state, $next){
        $this->word = $word;
        $this->state = $state;
        $this->next = $next;
    }
}
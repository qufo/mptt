<?php

/**
 * MPTT 模型 Trait
 * User: ufo
 * Date: 2018/5/20
 * Time: 10:53
 */

namespace Qufo\MPTT;


trait MPTTModel
{

    /**
     * 子孙节点数量,不包含自身.
     * @return float|int
     */
    public function ChildCount(){
        return ($this->rgt - $this->lft - 1 ) / 2;
    }

    /**
     * 子节点数量,不包含自身
     * @return mixed
     */
    public function SonCount(){
        return self::where('pid',$this->pid)->count();
    }

    /**
     * 路径, 不包含根
     * @return mixed
     */
    public function Path(){
        $root_id = $this->root_id ?: 1;
        $root = self::findOrFail($root_id);
        return self::whereBetween('lft',[$root->lft,$this->lft])
            ->whereBetween('rgt',[$this->rgt,$root->rgt])
            ->where('id','!=',$root_id)
            ->get();
    }

}
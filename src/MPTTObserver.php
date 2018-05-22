<?php

/**
 * 预排序遍历树算法 modified preorder tree traversal algorithm 观察者。
 * 在 App\Providers\AppServiceProvider 的 boot 方法中
 * ```
 * Tree::observe(\Qufo\MPTT\MPTTObserver::class);
 * ```
 */

namespace Qufo\MPTT;

use DB;

class MPTTObserver
{


    /**
     * 增加子级
     * @param $model
     * @return bool
     * @throws \Exception
     */
    public function created($model) {
        if ($model->id == 1) {
            DB::table($model->getTable())
                ->where('id',1)
                ->update([
                    'lft'   => 1,
                    'rgt'   => 2,
                    'lvl'   => 0
                ]);
            return true;
        }

        $parent = $model->findOrFail($model->pid);
        try {
            DB::beginTransaction();
            //所有左节点比父节点大的，都增加2
            DB::table($model->getTable())
                ->where('lft','>',$parent->lft)
                ->increment('lft',2);
            //所有右节点比父节点大的，都增加2
            DB::table($model->getTable())
                ->where('rgt','>',$parent->lft)
                ->increment('rgt',2);
            //更新当前结点
            DB::table($model->getTable())
                ->where('id',$model->id)
                ->update([
                    'lft'=>$parent->lft + 1,
                    'rgt'=>$parent->lft + 2,
                    'lvl'=>$parent->lvl + 1
                ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw  $e;
        }
        return false;
    }


    /**
     * 删除下属所有节点
     * @param $model
     * @return bool
     * @throws \Exception
     */
    public function deleting($model) {

        // 深度
        $width = $model->rgt - $model->lft + 1;
        try {
            DB::beginTransaction();
            // 删除下属
            DB::table($model->getTable())
                ->whereBetween('lft',[$model->lft + 1,$model->rgt - 1])
                ->delete();
            // 右值缩减
            DB::table($model->getTable())
                ->where('rgt','>',$model->rgt)
                ->decrement('rgt',$width);
            // 左值缩减
            DB::table($model->getTable())
                ->where('lft','>',$model->rgt)
                ->decrement('lft',$width);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rolBback();
            throw $e;
        }

        return false;
    }

}
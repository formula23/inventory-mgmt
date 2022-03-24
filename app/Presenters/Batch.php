<?php
/**
 * Created by PhpStorm.
 * User: danschultz
 * Date: 8/12/18
 * Time: 17:02
 */

namespace App\Presenters;


class Batch extends Presenters
{
    public function branded_name()
    {
        return ($this->entity->description ? $this->entity->description." (".$this->entity->name.")" : $this->entity->name);

        if(empty($this->entity->brand)) return $this->entity->name;
        return $this->entity->brand->name." - ".$this->entity->name;
    }

    public function thc_potency()
    {
        return (!empty($this->entity->COASourceBatch->thc)?$this->entity->COASourceBatch->thc:'-.--')."%";
    }

    public function cbd_potency()
    {
        return (!empty($this->entity->COASourceBatch->cbd)?$this->entity->COASourceBatch->cbd:'-.--')."%";
    }

    public function cbn_potency()
    {
        return (!empty($this->entity->COASourceBatch->cbn)?$this->entity->COASourceBatch->cbn:'-.--')."%";
    }

    public function thc_rnd_potency()
    {
        return ($this->entity->thc_rnd ? $this->entity->thc_rnd : '-.--')."%";
    }

    public function cbd_rnd_potency()
    {
        return ($this->entity->cbd_rnd ? $this->entity->cbd_rnd : '-.--')."%";
    }

    public function cbn_rnd_potency()
    {
        return ($this->entity->cbn_rnd ? $this->entity->cbn_rnd : '-.--')."%";
    }

}

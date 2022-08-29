<?php
namespace Farmadati\Get\Response;


class GetSchemaDataSetResult{
    public Fields $Fields;

    public function getFields(){
        return $this->Fields->Field;
    }
}

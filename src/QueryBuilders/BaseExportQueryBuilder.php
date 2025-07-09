<?php 

namespace Kodventure\LaravelDataExporter\QueryBuilders;

use Kodventure\LaravelDataExporter\Contracts\ExportQueryBuilderInterface;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseExportQueryBuilder implements ExportQueryBuilderInterface
{
    public Builder $query;
    
    abstract public function build(array $params): Builder;


    protected function finalize(Builder $query, array $params): Builder{
        $this->applyRemainFilters($params['remainFilters']);
        return $query;
    }

    private function applyRemainFilters(array $remainFilters){
        foreach ($remainFilters as $key => $value) {
            if(is_array($value)){
                $this->query->whereIn($key, $value);
            }else{
                $this->query->where($key, $value);
            }
        }
    }

}

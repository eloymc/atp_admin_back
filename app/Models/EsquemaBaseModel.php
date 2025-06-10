<?php  
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class EsquemaBaseModel extends Model
{
    public $schema = null;

    /**
     * Establece el esquema y la tabla.
     *
     * @param string $schema
     * @return static
     */
    public function setSchema(string $schema = null): static
    {
        //$this->schema = $schema;
        $this->schema = $schema ?? "vallejo";
        $this->updateTableWithSchema();
        return $this;
    }

    /**
     * Aplica el esquema a la propiedad $table.
     */
    protected function updateTableWithSchema()
    {
        if ($this->schema && $this->getRawTableName()) {
            $this->table = "{$this->schema}.{$this->getRawTableName()}";
        }
    }

    /**
     * Devuelve el nombre base de la tabla sin esquema.
     */
    protected function getRawTableName(): string
    {
        return property_exists($this, 'rawTable') ? $this->rawTable : $this->table;
    }

    /**
     * Se asegura de que la relación también use el mismo esquema.
     */
    public function newRelatedInstance($class)
    {
        $instance = parent::newRelatedInstance($class);

        if (method_exists($instance, 'setSchema') && $this->schema) {
            $instance->setSchema($this->schema);
        }

        return $instance;
    }

    public function hasManyWithSchema($related, $foreignKey = null, $localKey = null)
    {
        $instance = new $related;
        $instance->setSchema($this->schema);
        return $this->hasMany(get_class($instance), $foreignKey, $localKey);
    }

    public function hasOneWithSchema($related, $foreignKey = null, $localKey = null)
    {
        $instance = new $related;
        $instance->setSchema($this->schema);
        return $this->hasOne(get_class($instance), $foreignKey, $localKey);
    }

    public function belongsToWithSchema($related, $foreignKey = null, $localKey = null)
    {
        $instance = new $related;
        $instance->setSchema($this->schema);
        return $this->belongsTo(get_class($instance), $foreignKey, $localKey);
    }

    public function hasManyThroughWithSchema(
        $related,
        $through,
        $firstKey = null,
        $secondKey = null,
        $localKey = null,
        $secondLocalKey = null)
    {
        $relatedInstance = new $related;
        $throughInstance = new $through;

        $relatedInstance->setSchema($this->schema);
        $throughInstance->setSchema($this->schema);

        return $this->hasManyThrough(
            get_class($relatedInstance),
            get_class($throughInstance),
            $firstKey,
            $secondKey,
            $localKey,
            $secondLocalKey
        );
    }

    public function newRelatedInstanceFor(Model $parent, string $relation)
    {
        $instance = parent::newRelatedInstanceFor($parent, $relation);

        if (method_exists($instance, 'setSchema') && $this->schema) {
            $instance->setSchema($this->schema);
        }

        return $instance;
    }

    public function cargarRelacionesConEsquema(array $relaciones)
    {
        foreach ($relaciones as $relacion => $parametros) {
            $relacionModel = new $parametros['model'];
            $relacionModel->setSchema($this->schema);
            $relacionResultado = $relacionModel->where($parametros['foreign'], $this->{$parametros['local']})->first();
            $this->setRelation($relacion, $relacionResultado);
        }
    }
}

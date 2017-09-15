<?php
declare(strict_types = 1);
namespace CkTools\Utility;

trait TableUtilitiesTrait
{

    /**
     * Updates a single field for the given primaryKey
     *
     * @param mixed $primaryKey The primary key
     * @param string $field field name
     * @param string $value string value
     * @return bool True if the row was affected
     */
    public function updateField($primaryKey, $field, $value = null): bool
    {
        $query = $this->query()
            ->update()
            ->set([
                $field => $value
            ])
            ->where([
                $this->primaryKey() => $primaryKey
            ]);

        $statement = $query->execute();
        $success = $statement->rowCount() > 0;
        $statement->closeCursor();

        return $success;
    }
}

<?php

namespace Anil\FastApiCrud\Traits;

trait Crud
{
    public static function initializer($initializeModel='initializeModel')
    {
        $request = request();
        $sortBy = $request->sortBy;
        $desc = $request->descending === "true";
        $query = $request->get('query');
        $filters = json_decode($request->query('filters',""),true);
        $filters=$filters?array_merge($filters,$request->query()):$request->query();
        if (method_exists(static::class, $initializeModel)) {
            $model = static::$initializeModel();
        } else {
            $model = static::query();
        }

        /** If the model has any query then filter by query */
        if ($query) $model->queryfilter($query);
        foreach ($filters as $filter => $value) {
            if ($value !== null) {
                $method=ucfirst(Str::camel( $filter));
                if (method_exists(static::class, 'scope' .$method)) {
                    $model->{$method}($value);
                } elseif (method_exists($model, $filter)) {
                    $model->{$filter}($value);
                }
            }
        }

        if($sortBy && $sortBy !== 'null'){
            return $model->orderBy($sortBy, $desc ? 'desc':'asc');
        }

        return $model->latest();
    }

    public static function getTableName()
    {
        return (new self())->getTable();
    }


    public function uploadMultipleFilesToDisk(
        $value,
        $attribute_name,
        $disk,
        $destination_path
    )
    {
        $request = Request::instance();
        if (!is_array($this->{$attribute_name})) {
            $attribute_value = json_decode($this->{$attribute_name}, true) ?? [];
        } else {
            $attribute_value = $this->{$attribute_name};
        }
        $files_to_clear = $request->get('clear_' . $attribute_name);

        // if a file has been marked for removal,
        // delete it from the disk and from the db
        if ($files_to_clear) {
            foreach ($files_to_clear as $key => $filename) {
                Storage::disk($disk)->delete($filename);
                $attribute_value = array_where($attribute_value,
                    function ($value, $key) use ($filename) {
                        return $value != $filename;
                    });
            }
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if ($request->hasFile($attribute_name)) {
            foreach ($request->file($attribute_name) as $file) {
                if ($file->isValid()) {
                    // 1. Generate a new file name
                    $new_file_name = md5($file->getClientOriginalName() . random_int(1, 9999) . time()) . '.' . $file->getClientOriginalExtension();

                    // 2. Move the new file to the correct path
                    $file_path = $file->storeAs($destination_path,
                        $new_file_name, $disk);

                    // 3. Add the public path to the database
                    $attribute_value[] = $file_path;
                }
            }
        }

        $this->attributes[$attribute_name] = json_encode($attribute_value);
    }

    public function dateCombined()
    {
        $ad = $this->created_at->toDateString();
        $bs = (new MstDateConverter())->getNepDateCombined($ad);
        $time = $this->created_at->format('H:i');
        $ampm = $this->created_at->format('A');

        return '<span class="nepali-font">' . $bs . '</span> B.S.' . ' (' . $ad . ' A.D.)'
            . ' <span class="nepali-font">' . $time . ' </span> ' . $ampm;
    }

    public function uploadFileToDisk($value, $attribute_name, $disk, $destination_path)
    {
        $request = Request::instance();

        // if a new file is uploaded, delete the file from the disk
        if ($request->hasFile($attribute_name) && $this->{$attribute_name}
            && $this->{$attribute_name} != null
        ) {
            Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        /** if the file input is empty, delete the file from the disk */
        if (is_null($value) && $this->{$attribute_name} != null) {
            Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        /** if a new file is uploaded, store it on disk and its filename in the database */
        if ($request->hasFile($attribute_name)
            && $request->file($attribute_name)->isValid()
        ) {
            /** 1. Generate a new file name */
            $file = $request->file($attribute_name);
            $new_file_name = md5($file->getClientOriginalName() . random_int(1, 9999) . time()) . '.' . $file->getClientOriginalExtension();

            /** 2. Move the new file to the correct path */
            $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

            /** 3. Save the complete path to the database */
            $this->attributes[$attribute_name] = $file_path;
        }
    }
}

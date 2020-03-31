<?php

namespace App\Filter;

class RequestsFilter
{
    protected $builder;
    protected $request;

    public function __construct($builder, $request)
    {
        $this->builder = $builder;
        $this->request = $request;
    }

    public function apply()
    {
        foreach ($this->request->all() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }

    public function status($value)
    {
        if ($value === 'opened') {
            $this->builder = $this->builder->where('status', 1);
        } else if ($value === 'closed') {
            $this->builder = $this->builder->where('status', 0);
        }
    }

    public function is_checked($value)
    {
        $user = auth()->user();

        $this->builder = $this->builder->whereHas('dialogue', function ($q) use ($value, $user) {
            $q->where('author_id', '!=', $user->id)->where('is_checked', $value);
        });
    }

    public function resp($value)
    {
        $user = auth()->user();

        if ($value === "1") {
            $this->builder = $this->builder->whereHas('dialogue', function ($q) use ($value, $user) {
                $q->where('author_id', '=', $user->id);
            });
        } else if ($value === "0") {
            $this->builder = $this->builder->whereDoesntHave('dialogue', function ($q) use ($value, $user) {
                $q->where('author_id', '=', $user->id);
            });
        }
    }
}

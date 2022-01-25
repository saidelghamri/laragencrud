<?php

namespace LaraCrud\Contracts;

/**
 * Said El-Ghamri <saidelghamri010@gmail.com>.
 */
interface Crud
{
    /**
     * Process template and return complete code.
     *
     * @return mixed
     */
    public function template();

    /**
     * Get code and save to disk.
     *
     * @return mixed
     */
    public function save();
}

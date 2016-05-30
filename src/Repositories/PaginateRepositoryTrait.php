<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the paginate repository trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait PaginateRepositoryTrait
{
    /**
     * The paginated links generated by the paginate method.
     *
     * @var string
     */
    protected $paginateLinks;

    /**
     * Get a paginated list of the models.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate()
    {
        $model = $this->model;

        if (property_exists($model, 'order')) {
            $paginator = $model::orderBy($model::$order, $model::$sort)->paginate($model::$paginate, $model::$index);
        } else {
            $paginator = $model::paginate($model::$paginate, $model::$index);
        }

        if (!$this->isPageInRange($paginator) && !$this->isFirstPage($paginator)) {
            throw new NotFoundHttpException();
        }

        if (count($paginator)) {
            $this->paginateLinks = $paginator->render();
        }

        return $paginator;
    }

    /**
     * Is this current page in range?
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     *
     * @return bool
     */
    protected function isPageInRange(LengthAwarePaginator $paginator)
    {
        return $paginator->currentPage() <= ceil($paginator->lastItem() / $paginator->perPage());
    }

    /**
     * Is the current page the first page?
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     *
     * @return bool
     */
    protected function isFirstPage(LengthAwarePaginator $paginator)
    {
        return $paginator->currentPage() === 1;
    }

    /**
     * Get the paginated links.
     *
     * @return string
     */
    public function links()
    {
        return $this->paginateLinks;
    }
}

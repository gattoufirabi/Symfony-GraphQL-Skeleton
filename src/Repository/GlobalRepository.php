<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method object|null find($id, $lockMode = null, $lockVersion = null)
 * @method object|null findOneBy(array $criteria, array $orderBy = null)
 * @method object[]    findAll()
 * @method object[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class GlobalRepository extends ServiceEntityRepository
{
    public function __construct(private readonly ManagerRegistry $registry, $entityClass = '')
    {
        parent::__construct($registry, $entityClass);
    }

    public function remove(object $object): void
    {
        $this->registry->getManager()->remove($object);
    }

    public function resetManager(?string $name = null): void
    {
        $this->registry->resetManager($name);
    }

    public function persist(object $object): void
    {
        $this->registry->getManager()->persist($object);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }

    public function save(object $object): void
    {
        $this->persist($object);
        $this->flush();
    }
    public function ajaxTable(mixed $request): array
    {
        if (isset($request['columns'])) {
            $column = '';
            foreach ($this->cleanArray($request['columns']) as $columns) {
                if (isset($columns['field'])) {
                    $column .= preg_replace('/_/', '.', $columns['field'], 1) . ' AS ' . $columns['field'] . ',';
                }
            }
        } else {
            $column = 't';
        }

        $qb = $this->createQueryBuilder('t')->select(rtrim($column, ','));
        $total = $this->createQueryBuilder('t')->select('count(t.id)');

        $filteredTotal = clone $total;

        if (isset($request['join'])) {
            foreach ($request['join'] as $join) {
                if($join['type'] == 'left') {
                    $qb->leftJoin($join['join'], $join['alias'], Expr\Join::WITH, $join['condition']);
                    $total->leftJoin($join['join'], $join['alias'], Expr\Join::WITH, $join['condition']);
                    $filteredTotal->leftJoin($join['join'], $join['alias'], Expr\Join::WITH, $join['condition']);
                } else {
                    $qb->innerJoin($join['join'], $join['alias'], Expr\Join::WITH, $join['condition']);
                    $total->innerJoin($join['join'], $join['alias'], Expr\Join::WITH, $join['condition']);
                    $filteredTotal->innerJoin($join['join'], $join['alias'], Expr\Join::WITH, $join['condition']);
                }
            }
        }

        if (isset($request['offset']) && null != $request['offset']) {
            $qb->setFirstResult((int) $request['offset']);
        }

        if (isset($request['limit']) && null != $request['limit']) {
            $qb->setMaxResults((int) $request['limit']);
        }

        if (isset($request['order'], $request['sort'])) {
            $qb->addOrderBy($request['sort'], $request['order']);
        }
        if (isset($request['condition'])) {
            foreach ($request['condition'] as $condition) {
                $qb->andWhere($condition);
                $total->andWhere($condition);
                $filteredTotal->andWhere($condition);
            }
        }

        $search = [];
        if (isset($request['columns'], $request['search']) && '' != $request['search']) {
            foreach ($this->cleanArray($request['columns']) as $column) {
                // Customize search by converting search string to specifique type.
                if (isset($column['searchConfiguration'])) {
                    foreach ($column['searchConfiguration'] as $originalValue => $convertedValue) {
                        if (str_contains(strtolower($convertedValue), strtolower($request['search']))) {
                            $search[] = $qb->expr()->like(
                                preg_replace('/_/', '.', $column['field'], 1),
                                '\'%' . $originalValue . '%\''
                            );
                        }
                    }
                } else {
                    $search[] = $qb->expr()->like(
                        preg_replace('/_/', '.', $column['field'], 1),
                        '\'%' . $request['search'] . '%\''
                    );
                }
            }
        }

        if (0 != count($search)) {
            $qb->andWhere(new Expr\Orx($search));
            $filteredTotal->andWhere(new Expr\Orx($search));
        }

        try {
            $recordsTotal = $total->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            $recordsTotal = 0;
        }

        try {
            $recordsFiltered = $filteredTotal->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            $recordsFiltered = 0;
        }

        return [
            'request' => $request,
            'totalNotFiltered' => $recordsTotal,
            'total' => $recordsFiltered,
            'rows' => $qb->getQuery()->getScalarResult(),
        ];
    }


    protected function cleanArray($arr)
    {
        foreach ($arr as $k => $v) {
            foreach ($arr as $key => $value) {
                if ($k != $key && isset($value['field'], $v['field']) && $v['field'] == $value['field']) {
                    unset($arr[$k]);
                }
            }
        }

        return $arr;
    }
}

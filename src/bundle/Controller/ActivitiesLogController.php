<?php

namespace EzPlatform\ActivitiesLogBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use EzPlatform\ActivitiesLog\Repository\Services\ActivitiesLogInteractiveLoginService;
use EzPlatform\ActivitiesLog\Repository\Services\ActivitiesLogRepositoryService;
use EzPlatform\ActivitiesLogBundle\Entity\ActivitiesLog;
use Ibexa\Contracts\AdminUi\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;

class ActivitiesLogController extends BaseController
{
    private const PAGINATION_PARAM_NAME = 'page';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var \EzPlatform\ActivitiesLogBundle\Entity\ActivitiesLog */
    private $activitiesLog;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzPlatform\ActivitiesLog\Repository\Services\ActivitiesLogRepositoryService */
    private $activitiesLogRepositoryService;

    /** @var \EzPlatform\ActivitiesLog\Repository\Services\ActivitiesLogInteractiveLoginService */
    private $activitiesLogInteractiveLogin;

    /** @var int */
    private $activitiesLogUiPanelPaginationLimit;

    /** @var \EzPlatform\ActivitiesLog\Repository\User */
    private $user;

    /**
     * ActivitiesLogController constructor.
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \EzPlatform\ActivitiesLogBundle\Entity\ActivitiesLog $activitiesLog
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \EzPlatform\ActivitiesLog\Repository\Services\ActivitiesLogRepositoryService $activitiesLogRepositoryService
     * @param \EzPlatform\ActivitiesLog\Repository\Services\ActivitiesLogInteractiveLoginService $activitiesLogInteractiveLogin
     * @param int $activitiesLogUiPanelPaginationLimit
     * @param $user
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivitiesLog $activitiesLog,
        PermissionResolver $permissionResolver,
        ActivitiesLogRepositoryService $activitiesLogRepositoryService,
        ActivitiesLogInteractiveLoginService $activitiesLogInteractiveLogin,
        $activitiesLogUiPanelPaginationLimit,
        $user
    ) {
        $this->entityManager = $entityManager;
        $this->activitiesLog = $activitiesLog;
        $this->permissionResolver = $permissionResolver;
        $this->activitiesLogRepositoryService = $activitiesLogRepositoryService;
        $this->activitiesLogInteractiveLogin = $activitiesLogInteractiveLogin;
        $this->activitiesLogUiPanelPaginationLimit = $activitiesLogUiPanelPaginationLimit;
        $this->user = $user;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function allActivitiesAction(Request $request)
    {
        if (!$this->permissionResolver->hasAccess('ezplatformactivitieslog', 'activitieslog_all')) {
            return $this->render(
                '@ibexadesign/activities/activitieslog_view.html.twig',
                [
                    'access_denied' => 'access_denied',
                ]
            );
        }
        $page = $request->query->get(self::PAGINATION_PARAM_NAME, 1);
        $pagerfanta = $this->activitiesLogRepositoryService->getPageResults($this->activitiesLogUiPanelPaginationLimit, $page);

        return $this->render(
            '@ibexadesign/activities/activitieslog_view.html.twig',
            [
                'pagination' => $pagerfanta,
            ]
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function myActivitiesAction(Request $request)
    {
        if (!$this->permissionResolver->hasAccess('ezplatformactivitieslog', 'activitieslog_my')) {
            return $this->render(
                '@ibexadesign/activities/activitieslog_view.html.twig',
                [
                    'access_denied' => 'access_denied',
                ]
            );
        }

        $userId = $this->user->getCurrentUserId();

        $page = $request->query->get(self::PAGINATION_PARAM_NAME, 1);

        $pagerfanta = $this->activitiesLogRepositoryService->getPageResultsPerUser(
            $userId,
            $this->activitiesLogUiPanelPaginationLimit,
            $page
        );

        return $this->render(
            '@ibexadesign/activities/activitieslog_view.html.twig',
            [
                'pagination' => $pagerfanta,
                'userInteractiveLoginData' => $this->activitiesLogInteractiveLogin->getInteractiveLoginData($userId),
            ]
        );
    }
}

<?php
namespace Povs\ListerTwigBundle\Type\ListType;

use Povs\ListerBundle\Service\RequestHandler;
use Povs\ListerTwigBundle\Service\ListRenderer;
use Povs\ListerBundle\View\ListView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Throwable;
use Twig\Environment;

/**
 * @author Povilas Margaiatis <p.margaitis@gmail.com>
 */
class AjaxListType extends TwigListType
{
    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * AjaxListType constructor.
     *
     * @param Environment    $twig
     * @param ListRenderer   $renderer
     * @param RequestHandler $requestHandler
     */
    public function __construct(Environment $twig, ListRenderer $renderer, RequestHandler $requestHandler)
    {
        parent::__construct($twig, $renderer);
        $this->requestHandler = $requestHandler;
    }

    /**
     * @inheritDoc
     */
    public function generateResponse(ListView $listView, array $options): Response
    {
        $view = $this->getView($listView, $options);
        $response = $this->isAjaxRequest() ? new JsonResponse($view) : new Response($view);

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function configureSettings(OptionsResolver $optionsResolver): void
    {
        parent::configureSettings($optionsResolver);
        $optionsResolver->setDefined(['block']);
        $optionsResolver->setAllowedTypes('block', 'string');
        $optionsResolver->setDefault('block', 'list_table');
    }

    /**
     * @param ListView $listView
     * @param array    $options
     *
     * @return string
     */
    protected function getView(ListView $listView, array $options): string
    {
        $context = $this->buildContext($listView, $options);
        $context['list_data']['ajax'] = true;
        $this->renderer->setListTemplate($options['template']);

        if ($this->isAjaxRequest()) {
            $view = $this->renderer->render(
                $listView,
                $this->config['block'],
                $context,
                false
            );
        } else {
            $view = $this->twig->render($options['template'], $context);
        }

        return $view;
    }

    /**
     * @return bool
     */
    protected function isAjaxRequest(): bool
    {
        return $this->requestHandler->getRequest()->headers->has('ajax-request');
    }
}
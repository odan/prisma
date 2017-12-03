<?php

namespace App\Controller;

use App\Service\User\UserRepository;
use Slim\Http\Response;

/**
 * HomeController
 */
class HomeController extends AbstractController
{
    /**
     * @Inject
     * @var UserRepository
     */
    private $userRepo;

    /**
     * Index action
     *
     * @return Response
     */
    public function indexPage(): Response
    {
        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        $text = [
            'Loaded successfully!' => __('Loaded successfully!')
        ];

        $viewData = $this->getViewData([
            'text' => $text,
            'counter' => $counter,
            'url' => $this->request->getUri(),
            'secure' => $this->request->getAttribute('secure') ? __('Yes') : __('No'),
        ]);

        // Render template
        return $this->render('Home/home-index.twig', $viewData);
    }

    /**
     * Action (Json)
     *
     * @return Response Json response
     */
    public function load(): Response
    {
        $userId = $this->user->getId();
        $user = $this->userRepo->findById($userId);

        $result = [
            'message' => __('Loaded successfully!'),
            'now' => now(),
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ];

        return $this->response->withJson($result);
    }

    /**
     * Returns default text.
     *
     * @return array Array with translated text
     */
    protected function getText(): array
    {
        $text = parent::getText();

        $text['Current user'] = __('Current user');
        $text['User-ID'] = __('User-ID');
        $text['Username'] = __('Username');
        $text['Its'] = __("It's");

        return $text;
    }
}

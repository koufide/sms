<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

// use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Container\ContainerInterface;

use App\Service\MyLdap;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $entityManager;
    private $router;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $myLdap;
    private $container;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        MyLdap $myLdap,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->myLdap = $myLdap;
        $this->container = $container;
    }

    public function supports(Request $request)
    {
        // print("<br/>\n -------- stop--------supports");
        // dump($request);

        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $username = $request->request->get('username');

        $ldap_dom = '@' . $this->container->getParameter('ldap_dom');
        $username = str_replace($ldap_dom, '', $username);
        $username = strtolower($username);


        $credentials = [
            // 'username' => $request->request->get('username'),
            'username' => $username,
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        // dump($credentials);
        // print("<br/>\n -------- stop--------getCredentials");
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $credentials['username']]);

        ### verifier le champ isvalide

        // dump($user);
        // dump($credentials);
        // print("<br/>\n -------- stop--------getUser");
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Utilisateur introuvable.');
        }

        if ($user) {
            if (!$user->getIsActive()) {
                throw new CustomUserMessageAuthenticationException('Utilisateur desactivé.');
            }
        }


        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // dump($credentials);
        // dump($user);
        // print("<br/>\n -------- stop--------checkCredentials");



        //============================================================================
        //-------- AUTENTIFICATION VIA LDAP //// SSO
        //-------------------------------------------------

        $ldap_dom = '@' . $this->container->getParameter('ldap_dom');
        //$email = str_replace($ldap_dom, '', $email);
        //$email = strtolower($email);
        $ldap_filtre = $this->container->getParameter('ldap_filtre');
        $ldap_filtre = str_replace('{login}', $credentials['username'], $ldap_filtre);

        $tab = $this->myLdap->findUser(
            $this->container->getParameter('ldap_host'),
            $this->container->getParameter('ldap_port'),
            $this->container->getParameter('ldap_dn'),
            $ldap_dom,
            // $this->container->getParameter('ldap_user'),
            $credentials['username'],
            // $this->container->getParameter('ldap_password'),
            $credentials['password'],
            $ldap_filtre
        );

                // dump($tab);
        // exit("<br/>\n <br/>------quitter");

        if ($this->container->getParameter('ldap_dom') == 'ldap') {
            //============================================================================
            //-------- AUTENTIFICATION LDAP
            //-------------------------------------------------

            if (array_key_exists('erreur', $tab)) {
                        // return false;
                throw new CustomUserMessageAuthenticationException($tab['erreur']);
            } else {
                return true;
            }

        } else {//application

            //============================================================================
            //-------- AUTENTIFICATION APPLICATIVE
            //-------------------------------------------------

            //var_dump($this->passwordEncoder->isPasswordValid($user, $credentials['password']));
            // dd($this->passwordEncoder->isPasswordValid($user, $credentials['password']));
            return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        }

        //============================================================================

    }



    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // print("<br/>\n -------- stop--------onAuthenticationSuccess");
        // dump($this->getTargetPath($request->getSession(), $providerKey));
        // dd('onAuthenticationSuccess');

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->router->generate('some_route'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        return new RedirectResponse('index');
    }

    protected function getLoginUrl()
    {
        // print("<br/>\n -------- getLoginUrl--------onAuthenticationSuccess");
        // dump("getLoginUrl");
        // dd("getLoginUrl");
        return $this->router->generate('app_login');
    }
}

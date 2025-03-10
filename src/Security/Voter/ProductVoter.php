<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template TAttribute of string
 * @template TSubject of mixed
 *
 * @extends Voter<TAttribute, TSubject>
 */
final class ProductVoter extends Voter
{
    public const string EDIT = 'PRODUCT_EDIT';

    public const string DELETE = 'PRODUCT_DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    #[\Override]
    protected function supports(string $attribute, mixed $product): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE], true)) {
            return false;
        }

        return $product instanceof Product;
    }

    #[\Override]
    protected function voteOnAttribute($attribute, $product, TokenInterface $token): bool
    {
        // On récupère l'utilisateur à partir du token
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // On vérifie si l'utilisateur est admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // On vérifie les permissions
        /* switch($attribute){
            case self::EDIT:
                // On vérifie si l'utilisateur peut éditer
                return $this->canEdit();
                break;
            case self::DELETE:
                // On vérifie si l'utilisateur peut supprimer
                return $this->canDelete();
                break;
        } */

        /* if ($attribute == self::EDIT){
            // On vérifie si l'utilisateur peut éditer
            return $this->canEdit();
        }

        if ($attribute == self::DELETE){
            // On vérifie si l'utilisateur peut supprimer
            return $this->canDelete();
        } */

        // ** @var bool $action */
        // ** @var string $attribute */
        // $action =
        match ($attribute) {
            // On vérifie si l'utilisateur peut éditer
            self::EDIT => $this->canEdit(),
            // On vérifie si l'utilisateur peut supprimer
            self::DELETE => $this->canDelete(),
            default => throw new \Exception('Error: unknown action, the user has no such ability'),
        };

        return true;
    }

    private function canEdit(): bool
    {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}

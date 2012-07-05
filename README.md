# ACL bundle for Symfony2

This bundle integrates access permissions using the following relationship model:

**User** \<many-to-many> **Role** \<many-to-many> **Permission**

So one user can have multiple roles and each role may grant multiple permissions. Permissions are pulled from permission
containers that are tagged with `security.permission_container`. There should be a maximum of one permission container
per bundle.

## Instructions

1.  Your user class must implement `Briareos\AclBundle\Entity\AclSubject` interface.

1.  Map the interface to your user bundle, so that relationships can work

        # app/config/config.yml
        doctrine:
            orm:
                resolve_target_entities:
                    Briareos\AclBundle\Entity\AclSubject: App\UserBundle\Entity\User

1. Update your schema

        $ php app/console doctrine:schema:update --force

### If you're using *SonataAdminBundle*

1.  Chance the security handler to `sonata.admin.security.handler.briareos_acl`

        sonata_admin:
            security:
                handler: sonata.admin.security.handler.briareos_acl


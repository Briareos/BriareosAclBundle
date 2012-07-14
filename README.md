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

1.  Since the user entity must be on the owning side, map the AclRole entity from your user entity:

    Annotation:

        # App\UserBundle\Entity\User
        /**
         * @var ArrayCollection
         *
         * @ORM\ManyToMany(targetEntity="Briareos\AclBundle\Entity\AclRole", inversedBy="subjects")
         * @ORM\JoinTable(name="acl__subject_role",
         *  joinColumns={@ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")},
         *  inverseJoinColumns={@ORM\JoinColumn(name="aclRole_id", referencedColumnName="id", onDelete="CASCADE")}
         * )
         */
        private $aclRoles;

    Yaml:

        # App\UserBundle\Resources\config\doctrine\User.orm.yml
        joinTable:
            name: acl__subject_role
            inversedBy: subjects
            joinColumns:
                subject:
                    name: subject_id
                    referencedColumnName: id
                    onDelete: CASCADE
            inverseJoinColumns:
                aclRole:
                    name: aclRole_id
                    referencedColumnName: id
                    onDelete: CASCADE

1.  Update your schema

        $ php app/console doctrine:schema:update --force

1   Update your `config.yml` file to include addutional form resources:

        # app/config/config.yml
        twig:
            resources:
                form:
                    - "BriareosAclBundle::fields.html.twig"

### If you're using *SonataAdminBundle*

1.  Chance the security handler to `sonata.admin.security.handler.briareos_acl`

        sonata_admin:
            security:
                handler: sonata.admin.security.handler.briareos_acl


<?php

namespace Mercado_Solidario\Security;

// don't call the file directly
defined( 'ABSPATH' ) || die;

class CapabilitiesManager {
    // Roles
    public string $role_checkin = 'ms_checkin';
    public string $role_checkout = 'ms_checkout';
    public string $role_families = 'ms_families';
    public string $role_admin = 'ms_admin';

    // Capabilities
    public static string $checkin = 'ms_checkin';
    public static string $checkout = 'ms_checkout';
    public static string $families = 'ms_families';
    public static string $default = 'ms_default';
    public static string $stock = 'ms_stock';
    public static string $supplier = 'ms_supplier';

    public array $caps = [];

    public function __construct(){

        $this->caps = [
            'wordpress' => [
                'read' => true,
                'read_post' => true,
                'edit_post' => true,
                'edit_published_posts' => true,
                'edit_others_posts' => true,
                'publish_posts' => true,
                'edit_posts' => true
            ],

            'checkin' => [
                self::$default,
                self::$checkin,
                self::$stock,
                self::$supplier
            ],
            'checkout' => [
                self::$default,
                self::$checkout,
                self::$stock
            ],
            'families' => [
                self::$default,
                self::$families
            ],
            'admin' => [
                self::$default,
                self::$checkin,
                self::$checkout,
                self::$families,
                self::$stock,
                self::$supplier
            ]
        ];

        register_activation_hook(
            MERCADO_SOLIDARIO_DIR.'mercado-solidario.php',
            [$this, 'addCustomRoles']
        );

        register_deactivation_hook(
            MERCADO_SOLIDARIO_DIR.'mercado-solidario.php',
            [$this, 'removeCustomRoles']
        );

    }

    public function addCustomRoles(): void
    {

        $editor = get_role('editor');
        $shop_manager = get_role('shop_manager');
        $administrator = get_role('administrator');

        add_role(
            $this->role_checkin,
            'Mercado Solidario Entradas',
            array_fill_keys(
                $this->caps['checkin'],
                true
            ) + $this->caps['wordpress']
        );

        add_role(
            $this->role_checkout,
            'Mercado Solidario Saidas',
            array_fill_keys(
                $this->caps['checkout'],
                true
            ) + $this->caps['wordpress']
        );

        add_role(
            $this->role_families,
            'Mercado Solidario Famílias',
            array_fill_keys(
                $this->caps['families'],
                true
            ) + $this->caps['wordpress']
        );

        add_role(
            $this->role_admin,
            'Mercado Solidario Admin',
            array_fill_keys(
                $this->caps['admin'],
                true
            ) + $editor->capabilities
              + $shop_manager->capabilities
        );


        foreach ($this->caps['admin'] as $cap){
            $administrator->add_cap($cap);
        };

    }

    public function removeCustomRoles(): void
    {

        remove_role( $this->role_checkin );
        remove_role( $this->role_checkout );
        remove_role( $this->role_families );
        remove_role( $this->role_admin );

        $wp_adm_role = get_role('administrator');

        foreach ($this->caps['admin'] as $cap){
            $wp_adm_role->remove_cap($cap);
        };

    }

    public static function getFromClass(object|string $class): string
    {
        $ref = new \ReflectionClass($class);

        if (!$ref){
            return '';
        };

        $className = $ref->getShortName();

        $property = strtolower($className);

        $capClass = new \ReflectionClass(self::class);

        if (!$capClass->hasProperty($property)) {
            return $property;
        };

        $prop = $capClass->getProperty($property);

        if (!$prop->isPublic()) {
            return '';
        };

        return $prop->getValue();
    }

}
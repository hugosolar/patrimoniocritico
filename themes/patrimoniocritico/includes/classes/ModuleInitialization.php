<?php
/**
 * Auto-initialize all Module based classes in the theme.
 *
 * @package BlogInABoxTheme
 */

namespace PatrimonioCritico;

use Exception;
use HaydenPierce\ClassFinder\ClassFinder;
use ReflectionClass;
use ReflectionException;

/**
 * ModuleInitialization class.
 *
 * @package BlogInABoxTheme
 */
class ModuleInitialization {
	/**
	 * The class instance.
	 *
	 * @var null|ModuleInitialization
	 */
	private static $instance = null;

	/**
	 * Get the instance of the class.
	 *
	 * @return ModuleInitialization|null
	 */
	public static function instance(): ?ModuleInitialization {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Override the constructor, we don't want to init it that way.
	 */
	private function __construct() {
		// no-op. This class is a singleton.
	}

	/**
	 * The list of initialized classes.
	 *
	 * @var array
	 */
	protected array $classes = [];

	/**
	 * Get all the BlogInABoxTheme plugin classes.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function get_classes() {
		$class_finder = new ClassFinder();
		$class_finder::setAppRoot( PATRIMONIOCRITICO_THEME_PATH );
		return $class_finder::getClassesInNamespace( 'PatrimonioCritico', ClassFinder::RECURSIVE_MODE );
	}

	/**
	 * Initialize all the BlogInABoxTheme plugin classes.
	 *
	 * @return void
	 * @throws ReflectionException
	 */
	public function init_classes(): void {
		$load_class_order = [];

		foreach ( $this->get_classes() as $class ) {
			// Create a slug for the class name.
			$slug = $this->slugify_class_name( $class );

			// If the class has already been initialized, skip it.
			if ( isset( $this->classes[ $slug ] ) ) {
				continue;
			}

			// Create a new reflection of the class.
			$reflection_class = new ReflectionClass( $class );

			// Using reflection, check if the class can be initialized.
			// If not, skip.
			if ( ! $reflection_class->isInstantiable() ) {
				continue;
			}

			// Make sure the class is a subclass of Module, so we can initialize it.
			if ( ! $reflection_class->isSubclassOf( '\PatrimonioCritico\Module' ) ) {
				continue;
			}

			// Initialize the class.
			$instantiated_class = new $class();

			// Assign the classes into the order they should be initialized.
			$load_class_order[ intval( $instantiated_class->load_order ) ][] = [
				'slug'  => $slug,
				'class' => $instantiated_class,
			];
		}

		// Sort the initialized classes by load order.
		ksort( $load_class_order );

		// Loop through the classes and initialize them.
		foreach ( $load_class_order as $class_objects ) {
			foreach ( $class_objects as $class_object ) {
				$class = $class_object['class'];
				$slug  = $class_object['slug'];

				// If the class can be registered, register it.
				if ( $class->can_register() ) {
					// Call its register method.
					$class->register();
					// Store the class in the list of initialized classes.
					$this->classes[ $slug ] = $class;
				}
			}
		}
	}

	/**
	 * Slugify a class name.
	 *
	 * @param string $class_name The class name.
	 *
	 * @return string
	 */
	protected function slugify_class_name( string $class_name ): string {
		return sanitize_title( str_replace( '\\', '-', $class_name ) );
	}

	/**
	 * Get a class by its full class name, including namespace.
	 *
	 * @param string $class_name The class name & namespace.
	 *
	 * @return false|Module
	 */
	public function get_class( string $class_name ): Module|false {
		$class_name = $this->slugify_class_name( $class_name );

		if ( isset( $this->classes[ $class_name ] ) ) {
			return $this->classes[ $class_name ];
		}

		return false;
	}

	/**
	 * Get all the initialized classes.
	 *
	 * @return array
	 */
	public function get_all_classes(): array {
		return $this->classes;
	}
}

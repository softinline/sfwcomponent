<?php

    namespace Softinline\SfwComponent;

    /**
     * SfwFlags
     *
     * Runtime flags shared by every component of the same render tree.
     * A component can publish any flag while rendering and other components
     * can read it later, so they can react to each other.
     *
     * Flags are free keys: $flags['no_elements'] = true;
     */
    class SfwFlags extends \ArrayObject
    {

        /**
         * set
         * store a flag value
         */
        public function set(string $key, $value = true): void {

            $this[$key] = $value;

        }

        /**
         * has
         * check if a flag exists
         */
        public function has(string $key): bool {

            return $this->offsetExists($key);

        }

        /**
         * get
         * return a flag value
         */
        public function get(string $key, $default = null) {

            return $this->offsetExists($key) ? $this[$key] : $default;

        }

        /**
         * all
         * return all flags
         */
        public function all(): array {

            return $this->getArrayCopy();

        }

    }

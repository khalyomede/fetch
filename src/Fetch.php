<?php
	/*
	* Fetch
	*
	* Copyright © 2018 Khalyomede
	*
	* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
	*
	* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
	*
	* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	namespace Khalyomede;

	use InvalidArgumentException;
	use RuntimeException;
	use UnexpectedValueException;

	class Fetch {
		/**
		 * @var string
		 */
		public $folder_path;

		/**
		 * @var string
		 */
		public $cache_folder_path;

		/**
		 * @var bool
		 */
		public $cache_disabled;

		/**
		 * Set the default folder to fetch the data from
		 *
		 * @throws InvalidArgumentException
		 */
		public function __construct(string $folder_path) {
			if( file_exists($folder_path) === false ) {
				throw new InvalidArgumentException(sprintf('Fetch::using expects parameter 1 to be an existing folder, but no folder found at location "%s"', $folder_path));
			}

			if( is_dir($folder_path) === false ) {
				$folder_name = basename($folder_path);

				throw new InvalidArgumentException(sprintf('Fetch::using expects parameter 1 to be a folder, but "%s" seems to not be a folder', $folder_name));
			}

			$this->folder_path = rtrim($folder_path, '/');
			$this->cache_disabled = true;
			$this->cache_path_folder = '';
		}

		/**
		 * @throws InvalidArgumentException
		 */
		public function usingCache(string $cache_folder_path): Fetch {
			if( file_exists($cache_folder_path) === false ) {
				throw new InvalidArgumentException(sprintf('Fetch::usingCache expects parameter 1 to be an existing folder, "%s" not found', $cache_folder_path));
			}

			if( is_dir($cache_folder_path) === false ) {
				throw new InvalidArgumentException(sprintf('Fetch::usingCache expects parameter 1 to be a folder, but "%s" is not', $cache_folder_path));
			}

			$this->cache_folder_path = rtrim($cache_folder_path, '/');
			$this->cache_disabled = false;

			return $this;
		}

		public function disableCache(): Fetch {
			$this->cache_disabled = true;

			return $this;
		}

		public function enableCache(): Fetch {
			$this->cache_disabled = false;

			return $this;
		}

		/**
		 * @return mixed
		 * @throws InvalidArgumentException
		 * @throws UnexpectedValueException
		 * @throws RuntimeException
		 */
		public function from(string $path) {
      $hexadecimal_path = bin2hex($path);
			$cached_path = $this->cache_folder_path . "/$hexadecimal_path.php";

			if( $this->cache_disabled === false && file_exists($cached_path) ) {
				if( strlen($this->cache_folder_path) === 0 ) {
					throw new UnexpectedValueException('Fetch::from in cache mode expects you to set the cache folder path using Fetch::usingCache');
				}

				if( is_readable($cached_path) === false ) {
					throw new RuntimeException(sprintf('Fetch::from faced a non-readable file error for cached file "%s" (maybe it is open in another program ?)', $cached_path));
				}

				return require($cached_path);
			}
			else {
				$value = false;

				$parts = explode('.', $path);

				$file_found = false;
				$partial_path = $this->folder_path;

				foreach( $parts as $index => $part ) {
					if( strlen(trim($part)) === 0 ) {
						throw new InvalidArgumentException(sprintf('Fetch::from encountered an empty key for the key n.%s in the path "%s"', ($index + 1), $path));
					}

					if( $file_found === true ) {
						if( isset($value[$part]) === false ) {
							throw new InvalidArgumentException(sprintf('Fetch::from failed to resolve path "%s" because of the key n.%s "%s"', $path, ($index + 1), $part));
						}

						$value = $value[$part];
					}
					else if( is_file("$partial_path/$part.php") === true ) {
						$file_found = true;
						$value = require("$partial_path/$part.php");

						if( is_array($value) === false ) {
							throw new InvalidArgumentException(sprintf('Fetch::from expects parameter 1 to target a file return an array, "%s" return type found instead in the file located at "%s"', gettype($value), "$partial_path/$part.php"));
						}
					}
					else if( is_dir("$partial_path/$part") === true ) {
						if( is_readable("$partial_path/$part") === false ) {
							throw new RuntimeException(sprintf('Fetch::from could not open file at location "%s" (maybe it is already opened by another program?)', "$partial_path/$part"));
						}

						$partial_path .= "/$part";
					}
					else {
						throw new InvalidArgumentException(sprintf('Fetch::from could not resolve path "%s"', $path));
					}
				}

				if( $this->cache_disabled === false ) {
          $max_filename_size = 255;
          $extension_size = 4; // strlen(".php")

          if (strlen($hexadecimal_path) > $max_filename_size - $extension_size) {
            throw new InvalidArgumentExeption(sprintf('Fetch::from expects first parameter to be of size less than 251 characters but path was %d characters long', strlen($path)));
          }

					$php_code = var_export($value, true);

					file_put_contents($this->cache_folder_path . "/$hexadecimal_path.php", "<?php return $php_code; ?>");
				}

				return $value;
			}
		}
	}
?>

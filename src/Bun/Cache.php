<?php

/**
 * Cache
 **/
class Cache {    
    private $id;
    private $lifetime;
    private $filename;

    function __construct($id, $lifetime, $dir = './cache') {
        $this->id = md5($id);
        $this->lifetime = $lifetime;
        $this->filename = sprintf('%s/%s.tmp', realpath($dir), $this->id);
    }

    public function destroy()
    {
        if (file_exists($this->filename)) {
            return unlink($this->filename);
        }

        return FALSE;
    }

    /**
     * Start the caching process.
     * 
     * @return bool
     */
    public function start() {
        if ($this->lifetime > 0) {
            if (file_exists($this->filename)) {
                $current_time = new DateTime();

                $cached_file = explode('|', file_get_contents($this->filename), 2);
                $cached_time = DateTime::createFromFormat(DateTime::RFC2822, $cached_file[0]);

                if ($current_time < $cached_time) {

                    
                    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                        $if_modified_since = DateTime::createFromFormat(DateTime::RFC2822, $_SERVER['HTTP_IF_MODIFIED_SINCE']);
                     
                        if ($if_modified_since < $cached_time) {
                            header('HTTP/1.1 304 Not Modified');
                            exit();
                        } 
                    }

                    echo $cached_file[1];
                    return TRUE;
                }
            }
            
            // Start output buffering.
            ob_start();
        }

        return FALSE;
    }

    /**
     * End the caching process. 
     * 
     * @return bool
     */
    public function end() {
        if ($this->lifetime > 0) {

            // Stop output buffering, get and flush the content.
            $output = ob_get_flush();
            
            $current_time = new DateTime();
            
            // Set the expire date.
            $expires = new DateTime();
            $expires->modify($this->lifetime.' seconds');
            
            // Send some headers.
            header(sprintf('Expires: %s', $expires->format(DateTime::RFC2822)));
            header('Last-Modified: '. $current_time->format(DateTime::RFC2822));
            
            // Write output to file.
            file_put_contents($this->filename, sprintf('%s|%s', $expires->format(DateTime::RFC2822), $output));
        }
    }
}

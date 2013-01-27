<?php


  // here we do some initial php configuring
  // if you have your own configuration file
  // feel free to comment this block
  date_default_timezone_set ('UTC'); // yes, we use utc.






  abstract class FAbstract
    {

    // constants:
    // configure your fabstract objects here.
    const ___LOGFILE = '/var/log/nginx/error.log';

    // properties:
    // internal configuration for the object goes as a default value
    // on a public property to allways allow external access
    // we prefix them with ___ to avoid name collision


    // functions:
    // functions are not prefixed with ___ because we don't want
    // to type that much. any name collision should be fixed manually.


    // useful shortcuts for the output method       
    public function eco     ($message) { $this->output ($message, 'echo'); }
    public function br      ($message) { $this->output ($message, 'br');   }
    public function p       ($message, $label = null) { $this->output ($message, 'p', $label);    }
    public function log     ($message) { $this->output ($message, null, 'log');     }
    public function warning ($message) { $this->output ($message, null, 'warning'); }
    
    // also a shortcut, and throws an exception by default
    // if !$throw then no exception is launched
    public function error ($message, $throw = true)
      {
      $this->output ($message, null, 'error');

      if ($throw) 
        throw new Exception ('fabstract: ' . $message);

      return;
      }




    // outputs $json param as json
    // if $header we also send the json header
    // you can also hide some props
    // TODO: hide the props!
    public function json ($json = null, $hide = null, $header = true)
      {
      if (empty ($json))
        $json = $this;

      if ($json instanceof FAbstract)
        $json = $json->asJson ();

      if ($header && !headers_sent ())
        header('Content-type: application/json');

      if (empty ($hide))
        return json_encode ($json);
      else
        $this->error ('hide param is not implemented!!');

      }


    public function asJson ()
      {
      return $this;
      }




    // if $message is_object or is_array, we print_r, else we output plainly
    // if $mode === 'echo' we echo
    // if $mode === 'br' we echo with a <br/> after the message
    // if $mode === 'p' we echo inside a <p>
    // if there's a $label we prefix that to the message
    public function output ($message, $mode = null, $label = null)
      {
      if (is_object($message) || is_array ($message))
        $message = print_r ($message, 1);

      switch ($mode)
        {
        case 'echo': echo $message; break;
        case 'br': echo $message . '<br />'; break;
        case 'p': echo '<p' . (!empty ($label) ? ' class="' . $label . '"' : '') . '>' . $message . '</p>'; break;
        default:
          $message = '[' . date('r') . ']' . ($label === null ? '': $label . ' > ') . $message;

          if (self::___LOGFILE != null)
            {
            $file = fopen (self::___LOGFILE, 'a+');
            fwrite ($file, $message . "\n");
            fclose ($file);
            }
          else
            {
            error_log ($message);
            }
         }   

      return;
      }

    }






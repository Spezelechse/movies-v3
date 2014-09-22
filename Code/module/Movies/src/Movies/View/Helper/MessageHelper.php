<?php
namespace Movies\View\Helper;
use Zend\View\Helper\AbstractHelper;
 
class MessageHelper extends AbstractHelper
{
    public function __invoke($messenger, $size = 500)
    {
        $message = '<div class="center-block" style="max-width:'.$size.'px">';
        $message .= $messenger->renderCurrent('error',    array('alert', 'alert-dismissable', 'alert-danger'));
        $message .= $messenger->renderCurrent('info',    array('alert', 'alert-dismissable', 'alert-info'));
        $message .= $messenger->renderCurrent('default',    array('alert', 'alert-dismissable', 'alert-warning'));
        $message .= $messenger->renderCurrent('success',    array('alert', 'alert-dismissable', 'alert-success'));
        $message .= '</div>';
        
        return $message;
    }
}
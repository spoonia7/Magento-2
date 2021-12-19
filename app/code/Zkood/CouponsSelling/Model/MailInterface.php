<?php
namespace Zkood\CouponsSelling\Model;

interface MailInterface
{
    /**
     * Send email from request honda part form
     *
     * @param array $variables Email template variables
     * @return void
     * @since 100.2.0
     */
    public function send(array $variables);
}

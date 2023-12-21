<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Http\Response\Response;
use App\Jobs\SendEmailQueueJob;
use App\Models\Email;
use Exception;
use Illuminate\Http\JsonResponse;

class EmailSenderController extends Controller
{
    /**
     * @throws Exception
     */
    public function sendEmail(SendEmailRequest $request): JsonResponse
    {
        $response = new Response(now(), $request->fingerprint());

        $request->validated();

        $to = $request->json('to');
        $cc = $request->json('cc');
        $bcc = $request->json('bcc');
        $subject = $request->json('subject');
        $body = $request->json('body');
        $from = env('MAIL_FROM_ADDRESS');

        $data = [
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
            'body' => $body,
            'address' => $from
        ];

        SendEmailQueueJob::dispatch($data);

        $email = new Email();
        $email->to = $to;
        $email->cc = $cc;
        $email->bcc = $bcc;
        $email->subject = $subject;
        $email->body = $body;
        $email->sent = true;
        $email->save();

        return $response->setOKResponse([
            'message' => 'Email has been sent.'
        ]);
    }
}

<?php

namespace spec\CultureKings\Afterpay\Service\InStore;

use CultureKings\Afterpay;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class OrderSpec
 * @package spec\CultureKings\Afterpay\Service\InStore
 */
class OrderSpec extends ObjectBehavior
{
    function let(Afterpay\Model\InStore\Authorization $auth, Client $client, SerializerInterface $serializer)
    {
        $this->beConstructedWith($auth, $client, $serializer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Afterpay\Service\InStore\Order::class);
    }

    /**
     * @param Client|\PhpSpec\Wrapper\Collaborator                       $client
     * @param Stream|\PhpSpec\Wrapper\Collaborator                       $stream
     * @param Response|\PhpSpec\Wrapper\Collaborator                     $response
     * @param SerializerInterface|\PhpSpec\Wrapper\Collaborator          $serializer
     * @param Afterpay\Model\InStore\Order|\PhpSpec\Wrapper\Collaborator $order
     * @param HandlerStack|\PhpSpec\Wrapper\Collaborator                 $stack
     */
    function it_can_create_an_order(
        Client $client,
        Stream $stream,
        Response $response,
        SerializerInterface $serializer,
        Afterpay\Model\InStore\Order $order,
        HandlerStack $stack
    ) {
        $json = file_get_contents(__DIR__ . '/../../expectations/create_order_response.json');

        $serializer->serialize($order, 'json')->shouldBeCalled();
        $serializer->deserialize($json, Afterpay\Model\InStore\Order::class, 'json')->shouldBeCalled();

        $stream->__toString()->willReturn($json);
        $response->getBody()->willReturn($stream);
        $client->post('orders', Argument::any())->willReturn($response);

        $this->create($order, $stack);
    }

    /**
     * @param Client|\PhpSpec\Wrapper\Collaborator                       $client
     * @param SerializerInterface|\PhpSpec\Wrapper\Collaborator          $serializer
     * @param Afterpay\Model\ErrorResponse|\PhpSpec\Wrapper\Collaborator $errorResponse
     * @param Afterpay\Model\InStore\Order|\PhpSpec\Wrapper\Collaborator $order
     */
    function it_can_handle_create_order_error(
        Client $client,
        SerializerInterface $serializer,
        Afterpay\Model\ErrorResponse $errorResponse,
        Afterpay\Model\InStore\Order $order
    ) {
        $request = new Request('get', 'test');
        $response = new Response('400');

        $exception = new ClientException('ddssda', $request, $response);

        $client->post('orders', Argument::any())->willThrow($exception);

        $serializer->serialize($order, 'json')->shouldBeCalled();
        $serializer->deserialize(Argument::any(), Afterpay\Model\ErrorResponse::class, 'json')->shouldBeCalled()->willReturn($errorResponse);

        $this->shouldThrow(Afterpay\Exception\ApiException::class)->duringCreate($order);
    }

    /**
     * @param Client|\PhpSpec\Wrapper\Collaborator                          $client
     * @param Stream|\PhpSpec\Wrapper\Collaborator                          $stream
     * @param Response|\PhpSpec\Wrapper\Collaborator                        $response
     * @param SerializerInterface|\PhpSpec\Wrapper\Collaborator             $serializer
     * @param Afterpay\Model\InStore\Reversal|\PhpSpec\Wrapper\Collaborator $orderReversal
     * @param HandlerStack|\PhpSpec\Wrapper\Collaborator                    $stack
     */
    function it_can_reverse_an_order(
        Client $client,
        Stream $stream,
        Response $response,
        SerializerInterface $serializer,
        Afterpay\Model\InStore\Reversal $orderReversal,
        HandlerStack $stack
    ) {
        $json = file_get_contents(__DIR__ . '/../../expectations/order_reverse_response.json');

        $serializer->serialize($orderReversal, 'json')->shouldBeCalled();
        $serializer->deserialize($json, Afterpay\Model\InStore\Reversal::class, 'json')->shouldBeCalled();

        $stream->__toString()->willReturn($json);
        $response->getBody()->willReturn($stream);
        $client->post('orders/reverse', Argument::any())->willReturn($response);

        $this->reverse($orderReversal, $stack);
    }

    /**
     * @param Client|\PhpSpec\Wrapper\Collaborator                          $client
     * @param SerializerInterface|\PhpSpec\Wrapper\Collaborator             $serializer
     * @param Afterpay\Model\ErrorResponse|\PhpSpec\Wrapper\Collaborator    $errorResponse
     * @param Afterpay\Model\InStore\Reversal|\PhpSpec\Wrapper\Collaborator $orderReversal
     */
    function it_can_handle_reverse_order_error(
        Client $client,
        SerializerInterface $serializer,
        Afterpay\Model\ErrorResponse $errorResponse,
        Afterpay\Model\InStore\Reversal $orderReversal
    ) {
        $request = new Request('get', 'test');
        $response = new Response('400');

        $exception = new ClientException('ddssda', $request, $response);

        $client->post('orders/reverse', Argument::any())->willThrow($exception);

        $serializer->serialize($orderReversal, 'json')->shouldBeCalled();
        $serializer->deserialize(Argument::any(), Afterpay\Model\ErrorResponse::class, 'json')->shouldBeCalled()->willReturn($errorResponse);

        $this->shouldThrow(Afterpay\Exception\ApiException::class)->duringReverse($orderReversal);
    }
}

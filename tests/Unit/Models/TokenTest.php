<?php

namespace ImguBox\Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ImguBox\Tests\Support\FactoryTools;
use ImguBox\Tests\TestCase;
use ImguBox\Token;
use ImguBox\User;

class TokenTest extends Testcase
{
    use DatabaseMigrations, DatabaseTransactions;
    use FactoryTools;

    public function testItReturnDecryptedToken()
    {
        $user = $this->user();
        factory(Token::class)->create([
            'user_id'     => $user->id,
            'provider_id' => $this->dropboxProvider()->id,
            'token'       => 'myToken',
        ]);

        $databaseToken = Token::isDropboxToken()->first();

        $this->assertEquals('myToken', $databaseToken->token);
    }

    public function testItReturnsDecryptedRefreshToken()
    {
        $user = $this->user();
        factory(Token::class)->create([
            'user_id'       => $user->id,
            'provider_id'   => $this->imgurProvider()->id,
            'refresh_token' => 'myRefreshToken',
        ]);

        $databaseToken = Token::isImgurToken()->first();

        $this->assertEquals('myRefreshToken', $databaseToken->refresh_token);
    }

    public function testTokenReturnsAssociatedUser()
    {
        $user = $this->user();
        $this->imgurToken($user);

        $databaseToken = Token::isImgurToken()->first();

        $this->assertEquals($user->email, $databaseToken->user->email);
        $this->assertInstanceOf(User::class, $databaseToken->user);
    }
}

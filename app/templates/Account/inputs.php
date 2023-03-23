<?php
/**
 * @var \O0h\KantanFw\View\View $this
 * @var string $userName
 * @var string $password
 */
?>
<table>
    <tbody>
    <tr>
        <th>ユーザーID</th>
        <td>
            <input type="text" name="user_name" value="<?php echo $this->escape($userName); ?>" />
        </td>
    </tr>
    <tr>
        <th>パスワード</th>
        <td>
            <input type="password" name="password" value="<?php echo $this->escape($password); ?>">
        </td>

    </tr>

    </tbody>
</table>

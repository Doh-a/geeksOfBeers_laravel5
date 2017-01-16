<div id="comment_{{ $userComment->user_brasserie_id }}">
	<div>
		<a href="../user/{{{ $userComment->id_user }}}"><img src="../assets/img/avatars/{{ $userComment->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded"></a>
	</div>
	<div style="margin-left : 45px">
	 {{ $userComment->commentaire }}
	</div>
</div>

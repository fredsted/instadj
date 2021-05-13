<?php
require('../config.php');
require('lib.php');

if (!is_dir(PL_DIR)) {
	mkdir(PL_DIR);
}
if (!file_exists(CUR_FILE)) {
	file_put_contents(CUR_FILE, 'a');
}

function next_playlist_id()
{
	$last_playlist_id = file_get_contents(CUR_FILE);
	return base33encode(base33decode($last_playlist_id) + 1);
}

function sanitize_playlist_id(string $id)
{
	return preg_replace('/[^a-zA-Z0-9\s]/', '', $id);
}

function playlist_path(string $playlist_id)
{
	return PL_DIR . '/' . $playlist_id;
}

function playlist_password_path(string $playlist_id)
{
	return PL_DIR . '/' . $playlist_id . '.passwd';
}

function update_current_playlist_id(string $playlist_id)
{
	unlink(CUR_FILE);
	file_put_contents(CUR_FILE, $playlist_id);
}

function generate_playlist_password(string $playlist_id): string
{
	$password = sha1('password' . $playlist_id);
	file_put_contents(playlist_password_path($playlist_id), $password);
	return $password;
}

if (!isset($_GET['action'])) {
	error(1, 'Invalid action');
}

$action = $_GET['action'];

switch ($action) {
	case "store":
		$isNew = false;
		$password = null;

		if (isset($_GET['playlist_id'], $_GET['playlist_password'])) {

			$playlist_id = sanitize_playlist_id($_GET['playlist_id']);
			$password_path = playlist_password_path($playlist_id);

			if (file_exists($password_path)) {
				if ($_GET['playlist_password'] !== file_get_contents($password_path)) {
					error(19, 'Invalid password');
				}
			}
		} else {
			$playlist_id = next_playlist_id();
			$password = generate_playlist_password($playlist_id);
			$isNew = true;
		}

		if (!isset($_POST['playlistdata'])) {
			error(2, 'Invalid data');
		}

		// Write playlist data
		$data = base64_encode(htmlspecialchars($_POST['playlistdata'], ENT_NOQUOTES));
		file_put_contents(playlist_path($playlist_id), $data);

		// Update current playlist number
		if ($isNew) {
			update_current_playlist_id($playlist_id);
		}

		resp_json([
			'playlist_id' => $playlist_id,
			'password' => $password,
		]);
		break;

	case 'get':
		$password = null;
		if (empty($_GET['id'])) {
			$playlist_id = next_playlist_id();
			$password = generate_playlist_password($playlist_id);
			update_current_playlist_id($playlist_id);
		} else {
			$playlist_id = sanitize_playlist_id($_GET['id']);

			if (!file_exists(playlist_path($playlist_id))) {
				error(6, 'Invalid playlist');
			}
		}

		touch(playlist_path($playlist_id));

		$response = [
			'playlist_id' => $playlist_id,
			'password' => $password,
			'videos' => [],
		];

		$playlist_content = file_get_contents(playlist_path($playlist_id));
		if (!empty($playlist_content)) {
			$videoIds = array_keys(json_decode(base64_decode($playlist_content), true));
			$chunks = array_chunk($videoIds, 50, true);
			foreach ($chunks as $videoIds) {
				$result = ytget('videos?part=contentDetails,statistics,id,snippet&id=' . implode(',', $videoIds));

				if (isset($result) && !empty($result->items) && is_array($result->items)) {
					foreach ($result->items as $video) {
						$response['videos'][$video->id] = [
							'title' => $video->snippet->title,
							'duration' => isset($video->contentDetails, $video->contentDetails->duration)
								? ytv3duration($video->contentDetails->duration)
								: '',
						];
					}
				}
			}
		}

		resp_json($response);
		break;

	default:
		http_response_code(404);
		exit();
}
<?php

namespace App\Enums\ExceptionCodes;

enum CommentPolicyEnum: string
{
    case NO_PERMISSION_EDIT = 'no_permission_edit';
    case NO_PERMISSION_STATUS = 'no_permission_status';
    case NO_PERMISSION_FILE = 'no_permission_file';
    case FILE_MAX_COUNT = 'file_max_count';
    case PRIVATE_FEATURE_UNAVAILABLE_FOR_USER = 'private_feature_unavailable_for_user';
    case PRIVATE_FEATURE_UNAVAILABLE_FOR_ORGANISATION = 'private_feature_unavailable_for_organisation';
    case WEIGHT_NOT_ALLOW = 'weight_not_allow';
    case NOT_IN_PROJECT = 'not_in_project';
    case REACHED_LIMIT_STORAGE = 'reached_limit_storage';
}
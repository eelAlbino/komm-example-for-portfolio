<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Contracts\Docs\Swagger\Requests\StoreCommentRequest as SwaggerDoc;
use App\Models\Comment;

class GetFilesZipCommentRequest extends FormRequest implements SwaggerDoc
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        if ($request->id && $comment = Comment::find($request->id)) {
            return $this->user()->can('view', $comment);
        }
        return false;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'only' => 'sometimes|in:media,files'
        ];
    }
}

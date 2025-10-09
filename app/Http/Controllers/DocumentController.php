<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentUploadRequest;
use App\Models\Document;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(DocumentUploadRequest $request, PaymentRequest $paymentRequest)
    {
        // Authorization is handled by DocumentUploadRequest
        
        $files = $request->file('files') ?? [$request->file('file')];
        $uploadedCount = 0;
        
        foreach ($files as $file) {
            if ($file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('documents/' . $paymentRequest->id, $filename, 'public');

                Document::create([
                    'payment_request_id' => $paymentRequest->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $request->type,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now(),
                ]);
                
                $uploadedCount++;
            }
        }

        return redirect()->back()->with('success', "Đã tải lên {$uploadedCount} tài liệu");
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);

        return Storage::disk('public')->download($document->path, $document->original_name);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return redirect()->back()->with('success', 'Tài liệu đã được xóa');
    }
}

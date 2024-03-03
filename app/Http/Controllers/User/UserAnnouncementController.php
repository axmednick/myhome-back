<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementStatisticsResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class UserAnnouncementController extends Controller
{
    public function toggleIsActive($id){
        $announcement = Announcement::findOrFail($id);
        if ($announcement->user_id!=auth('sanctum')->id()){
            return response()->json('error',403);
        }
        $announcement->status = $announcement->status==1 ? 3 : 1;
        $announcement->save();
    }

    public  function deleteAnnouncement($id){
        $announcement = Announcement::findOrFail($id);
        if ($announcement->user_id!=auth('sanctum')->id()){
            return response()->json('error',403);
        }
        $announcement->delete();
    }

    public function announcementStatistics($id){
        $announcement = Announcement::findOrFail($id);

        return AnnouncementStatisticsResource::make($announcement);
    }


}

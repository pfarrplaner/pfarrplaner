<?php

use App\Liturgy\Item;
use App\Liturgy\Song;
use App\Liturgy\Songbook;
use App\Liturgy\SongReference;
use App\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('songbooks');
        Schema::create('songbooks', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('isbn')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('song_songbook');
        Schema::create('song_songbook', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('song_id');
            $table->unsignedBigInteger('songbook_id');
            $table->string('code')->nullable();
            $table->string('reference')->nullable();
        });

        $this->migrateSongbooks();

        $this->migrateLiturgyItems();

        /*
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['songbook', 'songbook_abbreviation', 'reference']);
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('songbooks');
        Schema::drop('song_songbook');
    }

    protected function migrateSongbooks() {
        $songbooks = [];
         foreach(Song::all() as $song) {
             if ($song->songbook_abbreviation) {
                 $songbooks[$song->songbook_abbreviation] = $song->songbook;
             }
         }
        foreach ($songbooks as $code => $songbook) {
            Songbook::create([
                'name' => $songbook,
                'code' => $code,
                                   ]);
        }

        // attach songs
        $delete = collect();
        foreach (Songbook::all() as $songbook) {
            $count = Song::where('songbook_abbreviation', $songbook->code)->count();
            echo "Migrating {$count} songs from {$songbook->name} ({$songbook->code})...\n";
            // attach to songs
            foreach (Song::where('songbook_abbreviation', $songbook->code)->get() as $song) {
                $song->songbooks()->sync([]);
                $song->songbooks()->attach([$songbook->id => ['reference' => $song->reference, 'code' => $song->songbook_abbreviation]]);

                // find duplicates for this song
                $duplicates = Song::where('title', $song->title)
                    ->where('id', '!=', $song->id)
                    ->where('songbook_abbreviation', '!=', $song->songbook_abbreviation)->get();
                foreach ($duplicates as $duplicate) {
                    echo "Found duplicate for Song #" . $song->id . ' ('
                        . $song->songbook_abbreviation . ' ' . $song->reference . ' ' . $song->title
                        . "): #" . $duplicate->id . ' (' . $duplicate->songbook_abbreviation . ' ' . $duplicate->reference . ")\n";
                    if ($duplicate->songbook_abbreviation) {
                        $sb2 = Songbook::where('code', $duplicate->songbook_abbreviation)->first();
                        if ($sb2) {
                            echo "Attaching songbook {$sb2->id} ({$sb2->code}), {$duplicate->reference} to song #{$song->id}\n";
                            if (!$song->songbooks->pluck('code')->contains($sb2->code)) {
                                $song->songbooks()->attach([$sb2->id => ['reference' => $duplicate->reference, 'code' => $duplicate->songbook_abbreviation]]);
                                $song->refresh();
                            } else {
                                echo "Song already has {$sb2->code} reference\n";
                            }
                        } else {
                            echo "No songbook with with code {$duplicate->songbook_abbreviation} found\n";
                        }
                    }
                    $delete->push($duplicate->id);
                }
            }
        }

        Song::whereIn('id', $delete)->delete();
    }

    protected function migrateLiturgyItems() {
        /** @var Item $item */
        foreach (Item::where('data_type', 'song')->get() as $item) {
            $data = $item->data;
            if (isset($data['song'])) {
                $reference = SongReference::where('code', $data['song']['songbook_abbreviation'])
                    ->where('reference', $data['song']['reference'])->first();
                if ($reference) {
                    $data['song'] = json_decode(json_encode($reference),true);
                    $item->update(['serialized_data' => serialize($data)]);
                } else {
                    if (trim($data['song']['songbook_abbreviation'].$data['song']['reference']))
                        echo "No reference found for LiturgyItem #{$item->id}: {$data['song']['songbook_abbreviation']} {$data['song']['reference']}\n";
                }
            }
        }
    }

};

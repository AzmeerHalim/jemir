<?php

namespace App\Http\Controllers\Itc;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use Illuminate\Http\Request;

class ClusterController extends Controller
{
    public function index()
    {
        $clusters = Cluster::withCount('studentProfiles')->get();
        return view('itc.clusters.index', compact('clusters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cluster_name' => ['required', 'string', 'max:255', 'unique:clusters,cluster_name'],
            'description' => ['nullable', 'string'],
        ]);

        Cluster::create($data);

        return redirect()->route('itc.clusters.index')->with('success', 'Cluster created.');
    }

    public function update(Request $request, Cluster $cluster)
    {
        $data = $request->validate([
            'cluster_name' => ['required', 'string', 'max:255', 'unique:clusters,cluster_name,' . $cluster->id],
            'description' => ['nullable', 'string'],
        ]);

        $cluster->update($data);

        return redirect()->route('itc.clusters.index')->with('success', 'Cluster updated.');
    }

    public function destroy(Cluster $cluster)
    {
        $cluster->delete();
        return redirect()->route('itc.clusters.index')->with('success', 'Cluster deleted.');
    }
}

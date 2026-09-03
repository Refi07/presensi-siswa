protected $fillable = ['student_id', 'type', 'start_date', 'end_date', 'reason', 'attachment_doc', 'status', 'verified_by'];

public function student() {
    return $this->belongsTo(Student::class, 'student_id');
}

public function verifier() {
    return $this->belongsTo(User::class, 'verified_by');
}
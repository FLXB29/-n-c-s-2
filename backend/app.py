from flask import Flask, jsonify, request
from flask_cors import CORS
import json
import os
from datetime import datetime

app = Flask(__name__)
CORS(app, resources={r"/api/*": {"origins": ["http://localhost:8080"]}}, supports_credentials=True)

DATA_DIR = os.path.join(os.path.dirname(__file__), 'data')
USERS_FILE = os.path.join(DATA_DIR, 'users.json')
EVENTS_FILE = os.path.join(DATA_DIR, 'events.json')
ORGANIZERS_FILE = os.path.join(DATA_DIR, 'organizers.json')

os.makedirs(DATA_DIR, exist_ok=True)

# Helpers

def read_json(path):
    if not os.path.exists(path):
        return []
    with open(path, 'r', encoding='utf-8') as f:
        try:
            return json.load(f)
        except Exception:
            return []


def write_json(path, data):
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)


# Seed data if empty
if not os.path.exists(USERS_FILE):
    write_json(USERS_FILE, [
        {"id": 1, "name": "Nguyễn Văn A", "email": "nguyenvana@email.com", "role": "User", "status": "active", "joinDate": "2024-01-15", "events": 5},
        {"id": 2, "name": "Trần Thị B", "email": "tranthib@email.com", "role": "Organizer", "status": "active", "joinDate": "2024-02-20", "events": 12},
        {"id": 3, "name": "Lê Văn C", "email": "levanc@email.com", "role": "User", "status": "inactive", "joinDate": "2024-03-10", "events": 2},
        {"id": 4, "name": "Phạm Thị D", "email": "phamthid@email.com", "role": "Organizer", "status": "pending", "joinDate": "2024-03-25", "events": 0}
    ])

if not os.path.exists(EVENTS_FILE):
    write_json(EVENTS_FILE, [
        {"id": 1, "title": "Tech Conference 2024", "organizer": "Trần Thị B", "status": "approved", "date": "2024-04-15", "attendees": 250, "revenue": 15000000},
        {"id": 2, "title": "Music Festival", "organizer": "Nguyễn Văn E", "status": "pending", "date": "2024-05-20", "attendees": 0, "revenue": 0},
        {"id": 3, "title": "Art Exhibition", "organizer": "Lê Thị F", "status": "rejected", "date": "2024-04-30", "attendees": 0, "revenue": 0},
        {"id": 4, "title": "Food Fair", "organizer": "Phạm Văn G", "status": "approved", "date": "2024-06-10", "attendees": 180, "revenue": 8500000}
    ])

if not os.path.exists(ORGANIZERS_FILE):
    write_json(ORGANIZERS_FILE, [
        {"id": 1, "name": "Trần Thị B", "company": "Tech Events Co.", "events": 12, "revenue": 45000000, "rating": 4.8, "status": "verified"},
        {"id": 2, "name": "Nguyễn Văn E", "company": "Music Productions", "events": 8, "revenue": 32000000, "rating": 4.5, "status": "pending"},
        {"id": 3, "name": "Lê Thị F", "company": "Art Gallery", "events": 5, "revenue": 18000000, "rating": 4.2, "status": "verified"},
        {"id": 4, "name": "Phạm Văn G", "company": "Food & Events", "events": 15, "revenue": 67000000, "rating": 4.9, "status": "verified"}
    ])


# API Routes

@app.get('/api/health')
def health():
    return jsonify({"status": "ok", "time": datetime.utcnow().isoformat() + 'Z'})


# Auth (mock)
@app.post('/api/auth/login')
def login():
    data = request.json or {}
    email = data.get('email')
    password = data.get('password')
    if not email or not password:
        return jsonify({"error": "Missing email or password"}), 400
    # Mock validation
    token = "fake-jwt-token"
    return jsonify({"token": token, "user": {"email": email, "role": "admin"}})


# Users
@app.get('/api/users')
def list_users():
    return jsonify(read_json(USERS_FILE))

@app.patch('/api/users/<int:user_id>')
def update_user(user_id):
    users = read_json(USERS_FILE)
    data = request.json or {}
    for u in users:
        if u['id'] == user_id:
            # Allow status updates and role changes
            if 'status' in data: u['status'] = data['status']
            if 'role' in data: u['role'] = data['role']
            write_json(USERS_FILE, users)
            return jsonify(u)
    return jsonify({"error": "User not found"}), 404

@app.delete('/api/users/<int:user_id>')
def delete_user(user_id):
    users = read_json(USERS_FILE)
    new_users = [u for u in users if u['id'] != user_id]
    if len(new_users) == len(users):
        return jsonify({"error": "User not found"}), 404
    write_json(USERS_FILE, new_users)
    return jsonify({"deleted": True})


# Events
@app.get('/api/events')
def list_events():
    return jsonify(read_json(EVENTS_FILE))

@app.post('/api/events')
def create_event():
    events = read_json(EVENTS_FILE)
    data = request.json or {}
    if not data.get('title'):
        return jsonify({"error": "Missing title"}), 400
    new_id = max([e['id'] for e in events], default=0) + 1
    event = {
        "id": new_id,
        "title": data.get('title'),
        "organizer": data.get('organizer', 'Unknown'),
        "status": "pending",
        "date": data.get('date') or datetime.utcnow().date().isoformat(),
        "attendees": 0,
        "revenue": 0
    }
    events.append(event)
    write_json(EVENTS_FILE, events)
    return jsonify(event), 201

@app.patch('/api/events/<int:event_id>')
def update_event(event_id):
    events = read_json(EVENTS_FILE)
    data = request.json or {}
    for e in events:
        if e['id'] == event_id:
            # Allow status changes and basic field updates
            for key in ['status', 'title', 'date', 'organizer', 'attendees', 'revenue']:
                if key in data:
                    e[key] = data[key]
            write_json(EVENTS_FILE, events)
            return jsonify(e)
    return jsonify({"error": "Event not found"}), 404


# Organizers
@app.get('/api/organizers')
def list_organizers():
    return jsonify(read_json(ORGANIZERS_FILE))

@app.patch('/api/organizers/<int:org_id>')
def update_organizer(org_id):
    organizers = read_json(ORGANIZERS_FILE)
    data = request.json or {}
    for o in organizers:
        if o['id'] == org_id:
            if 'status' in data: o['status'] = data['status']
            write_json(ORGANIZERS_FILE, organizers)
            return jsonify(o)
    return jsonify({"error": "Organizer not found"}), 404


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug=True)
from flask import Flask, request, jsonify
import face_recognition
import numpy as np
import base64
import cv2
import json
import os
import mysql.connector

app = Flask(__name__)

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'smartgarden'
}

def decode_base64_image(data):
    try:
        header, encoded = data.split(",", 1)
        img_bytes = base64.b64decode(encoded)
        nparr = np.frombuffer(img_bytes, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        return img
    except Exception as e:
        print(f"Image decode error: {str(e)}")
        raise ValueError(f"Invalid image data: {str(e)}")

@app.route('/register', methods=['POST'])
def register():
    try:
        username = request.json['username']
        face_data = request.json['face']
        img = decode_base64_image(face_data)

        encodings = face_recognition.face_encodings(img)
        if len(encodings) == 0:
            return jsonify({"success": False, "message": "No face detected"})

        face_encoding = encodings[0].tolist()

        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        cursor.execute("UPDATE utilisateur SET face_encoding = %s WHERE email = %s",
                       (json.dumps(face_encoding), username))
        
        if cursor.rowcount == 0:
            cursor.close()
            conn.close()
            return jsonify({"success": False, "message": "Email not found in database"})
        
        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({"success": True})
    except Exception as e:
        print(f"Registration error: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({"success": False, "message": f"Server error: {str(e)}"}), 500

@app.route('/login', methods=['POST'])
def login():
    try:
        face_data = request.json['face']
        img = decode_base64_image(face_data)
        encodings = face_recognition.face_encodings(img)
        if len(encodings) == 0:
            return jsonify({"success": False, "message": "No face detected"})

        login_encoding = encodings[0]

        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        cursor.execute("SELECT email, face_encoding FROM utilisateur WHERE face_encoding IS NOT NULL")
        for email, face_encoding_json in cursor.fetchall():
            known_encoding = np.array(json.loads(face_encoding_json))
            match = face_recognition.compare_faces([known_encoding], login_encoding, tolerance=0.5)
            if match[0]:
                cursor.close()
                conn.close()
                return jsonify({"success": True, "email": email})
        cursor.close()
        conn.close()
        return jsonify({"success": False, "message": "Face not recognized"})
    except Exception as e:
        print(f"Login error: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({"success": False, "message": f"Server error: {str(e)}"}), 500

if __name__ == '__main__':
    app.run(port=5000)
